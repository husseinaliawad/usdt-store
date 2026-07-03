<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsSender
{
    public function sendOtp(string $phone, string $code): void
    {
        $message = str_replace('{code}', $code, config('services.sms.otp_message'));

        match (config('services.sms.provider')) {
            'twilio' => $this->sendViaTwilio($phone, $message),
            'generic' => $this->sendViaGenericGateway($phone, $message),
            'log' => Log::info('OTP SMS', ['phone' => $phone, 'code' => $code]),
            default => throw new RuntimeException('Unsupported SMS provider.'),
        };
    }

    private function sendViaTwilio(string $phone, string $message): void
    {
        $sid = config('services.sms.twilio.sid');
        $token = config('services.sms.twilio.token');
        $from = config('services.sms.twilio.from');

        if (! $sid || ! $token || ! $from) {
            throw new RuntimeException('Twilio SMS credentials are missing.');
        }

        $response = Http::asForm()
            ->withBasicAuth($sid, $token)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $phone,
                'Body' => $message,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Twilio SMS failed: '.$response->body());
        }
    }

    private function sendViaGenericGateway(string $phone, string $message): void
    {
        $url = config('services.sms.generic.url');
        $token = config('services.sms.generic.token');

        if (! $url) {
            throw new RuntimeException('Generic SMS gateway URL is missing.');
        }

        $request = Http::acceptJson();

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->post($url, [
            config('services.sms.generic.to_field') => $phone,
            config('services.sms.generic.message_field') => $message,
            'from' => config('services.sms.generic.from'),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Generic SMS gateway failed: '.$response->body());
        }
    }
}
