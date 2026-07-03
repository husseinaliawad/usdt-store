<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmail;
use App\Models\Network;
use App\Models\OtpCode;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function requestOtp(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $code = (string) random_int(100000, 999999);

        OtpCode::create([
            'phone' => $data['email'],
            'email' => $data['email'],
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
        ]);

        SendOtpEmail::dispatch($data['email'], $code);

        return response()->json([
            'message' => 'تم إرسال رمز التحقق إلى الإيميل',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        $otp = OtpCode::where(function ($query) use ($data) {
            $query->where('email', $data['email'])
                ->orWhere('phone', $data['email']);
        })->whereNull('verified_at')->latest()->first();

        abort_if(! $otp || $otp->expires_at->isPast() || ! Hash::check($data['code'], $otp->code), 422, 'رمز التحقق غير صحيح');

        $otp->update(['verified_at' => now()]);

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'] ?? 'مستخدم USDT STORE',
                'phone' => $data['email'],
                'role' => 'user',
            ]
        );

        Network::where('is_active', true)->get()->each(function (Network $network) use ($user) {
            Wallet::firstOrCreate(
                ['user_id' => $user->id, 'network_id' => $network->id],
                [
                    'address' => 'USDT-'.$network->code.'-'.$user->id.'-'.strtoupper(fake()->bothify('????####')),
                    'is_primary' => $network->code === 'TRC20',
                ]
            );
        });

        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users')],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $data['phone'] ??= $data['email'];

        $user = User::create($data);
        return response()->json(['user' => $user], 201);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('wallets.network', 'kycVerification'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
