<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Network;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'] ?? 'USDT STORE User',
                'phone' => $data['email'],
                'role' => 'user',
            ]
        );

        $this->ensureWallets($user);

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
    private function ensureWallets(User $user): void
    {
        $depositAddresses = [
            'TRC20' => 'TBW6yG3RkgFZEUJpJ6U7Mhhi9shTQPRQDo',
            'SHAM_CASH' => 'FQy4HArVdBbZ87AHrbfdhSXRgyE5NUbrh6GaL8enMUeh',
        ];

        Network::where('is_active', true)->get()->each(function (Network $network) use ($user, $depositAddresses) {
            Wallet::updateOrCreate(
                ['user_id' => $user->id, 'network_id' => $network->id],
                [
                    'address' => $depositAddresses[$network->code]
                        ?? 'USDT-'.$network->code.'-'.$user->id.'-'.strtoupper(fake()->bothify('????####')),
                    'is_primary' => $network->code === 'TRC20',
                ]
            );
        });
    }
}
