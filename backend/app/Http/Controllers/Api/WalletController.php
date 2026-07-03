<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function home(Request $request)
    {
        $user = $request->user();
        $wallets = $user->wallets()->with('network')->get();
        $balance = $wallets->sum('balance');

        return response()->json([
            'balance_usdt' => number_format($balance, 2, '.', ''),
            'balance_usd' => number_format($balance, 2, '.', ''),
            'wallets' => $wallets,
            'networks' => Network::where('is_active', true)->get(),
            'latest_transactions' => $user->transactions()->with('network')->latest()->limit(8)->get(),
        ]);
    }

    public function receive(Request $request)
    {
        $data = $request->validate(['network_id' => ['required', 'exists:networks,id']]);
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $request->user()->id, 'network_id' => $data['network_id']],
            ['address' => 'USDT-WALLET-'.$request->user()->id.'-'.$data['network_id']]
        );

        Transaction::create([
            'user_id' => $request->user()->id,
            'network_id' => $data['network_id'],
            'type' => 'receive',
            'status' => 'completed',
            'amount' => 0,
            'wallet_address' => $wallet->address,
            'completed_at' => now(),
        ]);

        return response()->json($wallet->load('network'));
    }
}
