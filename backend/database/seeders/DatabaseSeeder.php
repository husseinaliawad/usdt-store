<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AppNotification;
use App\Models\ExchangeRate;
use App\Models\FeeSetting;
use App\Models\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['phone' => '+963900000000'],
            ['name' => 'USDT STORE Admin', 'email' => 'admin@usdt-store.local', 'password' => Hash::make('password'), 'role' => 'admin', 'kyc_status' => 'approved']
        );

        $user = User::updateOrCreate(
            ['phone' => '+963911111111'],
            ['name' => 'عميل تجريبي', 'email' => 'user@usdt-store.local', 'password' => Hash::make('password'), 'role' => 'user', 'kyc_status' => 'approved']
        );

        collect([
            ['name' => 'TRC20', 'code' => 'TRC20', 'withdraw_fee' => 1],
            ['name' => 'ERC20', 'code' => 'ERC20', 'withdraw_fee' => 8],
            ['name' => 'BEP20', 'code' => 'BEP20', 'withdraw_fee' => 1.5],
            ['name' => 'Arbitrum', 'code' => 'ARBITRUM', 'withdraw_fee' => 0.8],
            ['name' => 'Solana', 'code' => 'SOLANA', 'withdraw_fee' => 0.5],
        ])->each(fn ($network) => Network::updateOrCreate(['code' => $network['code']], $network + ['send_fee_percent' => 0.2]));

        Network::all()->each(fn ($network) => Wallet::updateOrCreate(
            ['user_id' => $user->id, 'network_id' => $network->id],
            ['address' => 'USDT-'.$network->code.'-DEMO-'.$user->id, 'balance' => $network->code === 'TRC20' ? 2450 : 0, 'is_primary' => $network->code === 'TRC20']
        ));

        FeeSetting::updateOrCreate(['type' => 'withdraw'], ['name' => 'رسوم السحب', 'fixed_fee' => 1, 'percent_fee' => 0.25, 'is_active' => true]);
        ExchangeRate::updateOrCreate(['base' => 'USDT', 'quote' => 'USD'], ['rate' => 1, 'is_active' => true]);
        AppNotification::updateOrCreate(['title' => 'مرحباً بك'], ['body' => 'مرحباً بك في USDT STORE.']);

        Transaction::updateOrCreate(['txid' => 'DEMO-DEPOSIT-1'], [
            'user_id' => $user->id,
            'network_id' => Network::where('code', 'TRC20')->value('id'),
            'type' => 'deposit',
            'status' => 'completed',
            'amount' => 750,
            'fee' => 0,
            'completed_at' => now(),
        ]);
    }
}
