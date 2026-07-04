<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('networks')->updateOrInsert(
            ['code' => 'SHAM_CASH'],
            [
                'name' => 'Sham Cash',
                'withdraw_fee' => 0,
                'send_fee_percent' => 0.2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('networks')
            ->where('code', 'SHAM_CASH')
            ->update(['is_active' => false, 'updated_at' => now()]);
    }
};
