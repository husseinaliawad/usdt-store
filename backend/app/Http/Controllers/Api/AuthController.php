<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Network;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! $user->password || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid email or password'], 422);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Account is disabled'], 403);
        }

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
            'password' => ['required', 'string', 'min:8'],
        ]);

        $data['phone'] ??= $data['email'];
        $data['role'] = 'user';

        $user = User::create($data);
        $this->ensureWallets($user);

        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
            'user' => $user,
        ], 201);
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
