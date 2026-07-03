<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otp_codes', function (Blueprint $table) {
            if (! Schema::hasColumn('otp_codes', 'email')) {
                $table->string('email')->nullable()->index()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('otp_codes', function (Blueprint $table) {
            if (Schema::hasColumn('otp_codes', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
