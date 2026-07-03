<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeSetting;
use App\Models\Network;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->transactions()->with('network')->latest();
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));
        $query->when($request->filled('type'), fn ($q) => $q->where('type', $request->type));
        return response()->json($query->paginate(20));
    }

    public function show(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === $request->user()->id || $request->user()->role === 'admin', 403);
        return response()->json($transaction->load('network'));
    }

    public function send(Request $request)
    {
        $this->requireKyc($request);
        $data = $request->validate([
            'network_id' => ['required', 'exists:networks,id'],
            'wallet_address' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $network = Network::findOrFail($data['network_id']);
        $fee = round(((float) $data['amount'] * (float) $network->send_fee_percent) / 100, 6);
        $tx = Transaction::create($data + ['user_id' => $request->user()->id, 'type' => 'send', 'fee' => $fee, 'status' => 'pending']);
        return response()->json($tx->load('network'), 201);
    }

    public function deposit(Request $request)
    {
        $data = $request->validate([
            'network_id' => ['required', 'exists:networks,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'txid' => ['required', 'string', 'max:255'],
            'proof' => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file('proof')->store('proofs', 'public');
        $tx = Transaction::create($data + ['user_id' => $request->user()->id, 'type' => 'deposit', 'proof_path' => $path, 'status' => 'pending']);
        return response()->json($tx->load('network'), 201);
    }

    public function withdraw(Request $request)
    {
        $this->requireKyc($request);
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'withdraw_method' => ['required', 'string', 'max:120'],
            'recipient_payload' => ['required', 'array'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $fee = FeeSetting::where('type', 'withdraw')->where('is_active', true)->first();
        $value = ($fee?->fixed_fee ?? 0) + (((float) $data['amount'] * ($fee?->percent_fee ?? 0)) / 100);
        $tx = Transaction::create($data + ['user_id' => $request->user()->id, 'type' => 'withdraw', 'fee' => $value, 'status' => 'pending']);
        return response()->json($tx, 201);
    }

    public function stats(Request $request)
    {
        $base = $request->user()->transactions();
        return response()->json([
            'sent_total' => (clone $base)->where('type', 'send')->sum('amount'),
            'received_total' => (clone $base)->where('type', 'receive')->sum('amount'),
            'fees_total' => (clone $base)->sum('fee'),
        ]);
    }

    private function requireKyc(Request $request): void
    {
        abort_unless($request->user()->kyc_status === 'approved', 403, 'يجب توثيق الهوية قبل تنفيذ العملية');
    }
}
