<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('عدد المستخدمين', User::count()),
            Stat::make('عمليات معلقة', Transaction::where('status', 'pending')->count()),
            Stat::make('أرصدة المحافظ', number_format((float) Wallet::sum('balance'), 2).' USDT'),
            Stat::make('إجمالي الإيداعات', number_format((float) Transaction::where('type', 'deposit')->where('status', 'completed')->sum('amount'), 2).' USDT'),
            Stat::make('إجمالي السحوبات', number_format((float) Transaction::where('type', 'withdraw')->where('status', 'completed')->sum('amount'), 2).' USDT'),
            Stat::make('رسوم اليوم', number_format((float) Transaction::whereDate('created_at', today())->sum('fee'), 2).' USDT'),
        ];
    }
}
