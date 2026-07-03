<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('withdraw_fee', 18, 6)->default(1);
            $table->decimal('send_fee_percent', 8, 4)->default(0.2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('network_id')->constrained()->cascadeOnDelete();
            $table->string('address');
            $table->decimal('balance', 20, 6)->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'network_id']);
        });

        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index();
            $table->string('code');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->string('id_image_path');
            $table->string('selfie_image_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('network_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['send', 'receive', 'deposit', 'withdraw']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'failed'])->default('pending');
            $table->decimal('amount', 20, 6);
            $table->decimal('fee', 20, 6)->default(0);
            $table->string('wallet_address')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('withdraw_method')->nullable();
            $table->json('recipient_payload')->nullable();
            $table->string('txid')->nullable();
            $table->string('proof_path')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base')->default('USDT');
            $table->string('quote')->default('USD');
            $table->decimal('rate', 20, 6)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fee_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['send', 'receive', 'deposit', 'withdraw']);
            $table->decimal('fixed_fee', 20, 6)->default(0);
            $table->decimal('percent_fee', 8, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('payload')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('fee_settings');
        Schema::dropIfExists('exchange_rates');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('kyc_verifications');
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('networks');
    }
};
