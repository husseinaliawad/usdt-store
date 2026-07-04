<?php

namespace App\Http\Controllers;

use App\Models\FeeSetting;
use App\Models\Network;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AdminTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function loginForm(): View|RedirectResponse
    {
        if ($this->isAdmin()) {
            return redirect()->route('control.dashboard');
        }

        return view('control.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $adminEmail = config('control.admin_email', 'admin@usdt-store.local');
            $adminPassword = config('control.admin_password', 'password');

            if ($credentials['email'] === $adminEmail && hash_equals($adminPassword, $credentials['password'])) {
                $admin = User::updateOrCreate(
                    ['email' => $adminEmail],
                    [
                        'name' => 'USDT STORE Admin',
                        'phone' => config('control.admin_phone', '+963900000000'),
                        'password' => Hash::make($adminPassword),
                        'role' => 'admin',
                        'kyc_status' => 'approved',
                        'is_active' => true,
                    ]
                );

                Auth::login($admin, $request->boolean('remember'));
            } else {
                return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])->onlyInput('email');
            }
        }

        if (! Auth::check()) {
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        if (! $this->isAdmin()) {
            Auth::logout();

            return back()->withErrors(['email' => 'هذا الحساب لا يملك صلاحية الإدارة.']);
        }

        return redirect()->route('control.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('control.login');
    }

    public function dashboard(): View|RedirectResponse
    {
        if (! $this->isAdmin()) {
            return redirect()->route('control.login');
        }

        return view('control.dashboard', [
            'stats' => [
                'users' => User::count(),
                'pending' => Transaction::where('status', 'pending')->count(),
                'walletBalance' => Wallet::sum('balance'),
                'todayFees' => Transaction::whereDate('created_at', today())->sum('fee'),
            ],
            'transactions' => Transaction::with(['user', 'network'])->latest()->limit(30)->get(),
            'users' => User::latest()->limit(30)->get(),
            'wallets' => Wallet::with(['user', 'network'])->latest('updated_at')->limit(30)->get(),
            'networks' => Network::orderBy('name')->get(),
            'fees' => FeeSetting::orderBy('type')->get(),
        ]);
    }

    public function approveTransaction(Transaction $transaction, AdminTransactionService $service): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $service->approve($transaction);

        return back()->with('status', 'تم قبول العملية.');
    }

    public function rejectTransaction(Request $request, Transaction $transaction, AdminTransactionService $service): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $data = $request->validate(['note' => ['required', 'string', 'max:500']]);
        $service->reject($transaction, $data['note']);

        return back()->with('status', 'تم رفض العملية.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $data = $request->validate([
            'role' => ['required', 'in:admin,user'],
            'kyc_status' => ['required', 'in:not_submitted,pending,approved,rejected'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update($data + ['is_active' => false]);

        return back()->with('status', 'تم تحديث المستخدم.');
    }

    public function storeWallet(Request $request): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'network_id' => ['required', 'exists:networks,id'],
            'address' => ['required', 'string', 'max:255'],
            'balance' => ['required', 'numeric', 'min:0'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        Wallet::updateOrCreate(
            ['user_id' => $data['user_id'], 'network_id' => $data['network_id']],
            $data + ['is_primary' => false]
        );

        return back()->with('status', 'تم حفظ المحفظة.');
    }

    public function updateWallet(Request $request, Wallet $wallet): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $data = $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'balance' => ['required', 'numeric', 'min:0'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $wallet->update($data + ['is_primary' => false]);

        return back()->with('status', 'تم تحديث المحفظة.');
    }

    public function storeNetwork(Request $request): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'max:60'],
            'withdraw_fee' => ['required', 'numeric', 'min:0'],
            'send_fee_percent' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Network::updateOrCreate(['code' => $data['code']], $data + ['is_active' => false]);

        return back()->with('status', 'تم حفظ الشبكة.');
    }

    public function updateFee(Request $request, FeeSetting $fee): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $data = $request->validate([
            'fixed_fee' => ['required', 'numeric', 'min:0'],
            'percent_fee' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $fee->update($data + ['is_active' => false]);

        return back()->with('status', 'تم تحديث العمولة.');
    }

    private function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin' && Auth::user()->is_active;
    }

    private function abortUnlessAdmin(): void
    {
        abort_unless($this->isAdmin(), 403);
    }
}
