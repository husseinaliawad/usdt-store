<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOtpEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public string $email,
        public string $code,
    ) {}

    public function handle(): void
    {
        Mail::raw("رمز التحقق الخاص بك في USDT STORE هو: {$this->code}", function ($message) {
            $message->to($this->email)->subject('رمز التحقق - USDT STORE');
        });
    }
}
