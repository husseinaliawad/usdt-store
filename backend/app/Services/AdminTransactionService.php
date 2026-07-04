<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdminTransactionService
{
    public function approve(Transaction $transaction, ?string $note = null): void
    {
        DB::transaction(function () use ($transaction, $note) {
            $transaction->refresh();

            if ($transaction->status !== 'pending') {
                return;
            }

            match ($transaction->type) {
                'deposit', 'receive' => $this->creditWallet($transaction),
                'withdraw', 'send' => $this->debitWallet($transaction),
                default => null,
            };

            $transaction->forceFill([
                'status' => 'completed',
                'note' => $note ?: $transaction->note,
                'completed_at' => now(),
            ])->save();

            $this->notifyUser(
                $transaction,
                'تمت الموافقة على العملية',
                'تمت الموافقة على عملية '.$this->typeLabel($transaction->type).' بقيمة '.$transaction->amount.' USDT.'
            );
            $this->audit('approved_transaction', $transaction);
        });
    }

    public function reject(Transaction $transaction, ?string $note = null): void
    {
        DB::transaction(function () use ($transaction, $note) {
            $transaction->refresh();

            if ($transaction->status !== 'pending') {
                return;
            }

            $transaction->forceFill([
                'status' => 'rejected',
                'note' => $note ?: $transaction->note,
            ])->save();

            $this->notifyUser(
                $transaction,
                'تم رفض العملية',
                'تم رفض عملية '.$this->typeLabel($transaction->type).' بقيمة '.$transaction->amount.' USDT.'
            );
            $this->audit('rejected_transaction', $transaction);
        });
    }

    private function creditWallet(Transaction $transaction): void
    {
        $wallet = $this->walletFor($transaction);
        $wallet->increment('balance', (float) $transaction->amount);
    }

    private function debitWallet(Transaction $transaction): void
    {
        $wallet = $this->walletFor($transaction);
        $total = (float) $transaction->amount + (float) $transaction->fee;

        if ((float) $wallet->balance < $total) {
            throw new RuntimeException('رصيد المستخدم غير كاف لإتمام العملية.');
        }

        $wallet->decrement('balance', $total);
    }

    private function walletFor(Transaction $transaction): Wallet
    {
        $query = Wallet::query()->where('user_id', $transaction->user_id);

        if ($transaction->network_id) {
            $query->where('network_id', $transaction->network_id);
        } else {
            $query->where('is_primary', true);
        }

        $wallet = $query->lockForUpdate()->first();

        if (! $wallet) {
            throw new RuntimeException('لا توجد محفظة مناسبة لهذه العملية.');
        }

        return $wallet;
    }

    private function notifyUser(Transaction $transaction, string $title, string $body): void
    {
        AppNotification::create([
            'user_id' => $transaction->user_id,
            'title' => $title,
            'body' => $body,
        ]);
    }

    private function audit(string $action, Transaction $transaction): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => Transaction::class,
            'auditable_id' => $transaction->id,
            'payload' => [
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
            ],
            'ip' => request()?->ip(),
        ]);
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'deposit' => 'الإيداع',
            'withdraw' => 'السحب',
            'send' => 'الإرسال',
            'receive' => 'الاستلام',
            default => $type,
        };
    }
}
