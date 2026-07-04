<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\FeeSetting;
use App\Models\KycVerification;
use App\Models\Network;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AdminTransactionService;
use Illuminate\Database\Eloquent\Builder;
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

    public function dashboard(Request $request): View|RedirectResponse
    {
        return $this->dashboardView($request, 'overview');
    }

    public function section(Request $request, string $section): View|RedirectResponse
    {
        if (! array_key_exists($section, $this->sections())) {
            abort(404);
        }

        return $this->dashboardView($request, $section);
    }

    private function dashboardView(Request $request, string $section): View|RedirectResponse
    {
        if (! $this->isAdmin()) {
            return redirect()->route('control.login');
        }

        $query = $request->string('q')->toString();
        $status = $request->string('status')->toString();

        $transactions = Transaction::with(['user', 'network'])
            ->when($status !== '', fn (Builder $builder) => $builder->where('status', $status))
            ->when($query !== '', function (Builder $builder) use ($query) {
                $builder->where(function (Builder $nested) use ($query) {
                    if (ctype_digit($query)) {
                        $nested->where('id', (int) $query);
                    }

                    $nested->orWhere('txid', 'like', "%{$query}%")
                        ->orWhere('wallet_address', 'like', "%{$query}%")
                        ->orWhereHas('user', fn (Builder $user) => $user
                            ->where('email', 'like', "%{$query}%")
                            ->orWhere('name', 'like', "%{$query}%"));
                });
            })
            ->latest()
            ->limit(40)
            ->get();

        $users = User::withCount(['wallets', 'transactions'])
            ->when($query !== '', fn (Builder $builder) => $builder
                ->where('email', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%"))
            ->latest()
            ->limit(40)
            ->get();

        $dailyMovement = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = today()->subDays($daysAgo);

            return [
                'label' => $date->format('m/d'),
                'amount' => (float) Transaction::whereDate('created_at', $date)->sum('amount'),
                'count' => Transaction::whereDate('created_at', $date)->count(),
            ];
        });

        $monthlyMovement = collect(range(5, 0))->map(function (int $monthsAgo) {
            $date = today()->subMonths($monthsAgo);

            return [
                'label' => $date->format('Y-m'),
                'amount' => (float) Transaction::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
                'count' => Transaction::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });

        return view('control.dashboard', [
            'stats' => [
                'balance' => Wallet::sum('balance'),
                'users' => User::count(),
                'todayTransfers' => Transaction::whereDate('created_at', today())->count(),
                'pendingTransfers' => Transaction::where('status', 'pending')->count(),
                'completedTransfers' => Transaction::where('status', 'completed')->count(),
                'profits' => Transaction::whereIn('status', ['approved', 'completed'])->sum('fee'),
                'collectedFees' => Transaction::sum('fee'),
                'activeWallets' => Wallet::count(),
                'kycPending' => KycVerification::where('status', 'pending')->count(),
                'notifications' => AppNotification::count(),
            ],
            'dailyMovement' => $dailyMovement,
            'monthlyMovement' => $monthlyMovement,
            'statusCounts' => Transaction::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'typeCounts' => Transaction::query()->selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type'),
            'transactions' => $transactions,
            'users' => $users,
            'wallets' => Wallet::with(['user', 'network'])->latest('updated_at')->limit(40)->get(),
            'kycVerifications' => KycVerification::with('user')->latest()->limit(20)->get(),
            'notifications' => AppNotification::with('user')->latest()->limit(20)->get(),
            'auditLogs' => AuditLog::with('user')->latest()->limit(25)->get(),
            'networks' => Network::orderBy('name')->get(),
            'fees' => FeeSetting::orderBy('type')->get(),
            'filters' => ['q' => $query, 'status' => $status],
            'page' => $section,
            'sections' => $this->sections(),
        ]);
    }

    public function approveTransaction(Transaction $transaction, AdminTransactionService $service): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $service->approve($transaction);
        $this->recordAudit('transaction.approve', $transaction, request());

        return back()->with('status', 'تم قبول العملية.');
    }

    public function rejectTransaction(Request $request, Transaction $transaction, AdminTransactionService $service): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $data = $request->validate(['note' => ['required', 'string', 'max:500']]);
        $service->reject($transaction, $data['note']);
        $this->recordAudit('transaction.reject', $transaction, $request, ['note' => $data['note']]);

        return back()->with('status', 'تم رفض العملية.');
    }

    public function updateTransactionStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->abortUnlessAdmin();
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,completed,failed'],
            'note' => ['nullable', 'string', 'max:500'],
            'txid' => ['nullable', 'string', 'max:255'],
        ]);

        $transaction->update([
            'status' => $data['status'],
            'note' => $data['note'] ?? $transaction->note,
            'txid' => $data['txid'] ?? $transaction->txid,
            'completed_at' => $data['status'] === 'completed' ? now() : $transaction->completed_at,
        ]);

        $this->recordAudit('transaction.status_update', $transaction, $request, $data);

        return back()->with('status', 'تم تحديث حالة العملية.');
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
        $this->recordAudit('user.update', $user, $request, $data);

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
        $this->recordAudit('wallet.store', null, $request, $data);

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
        $this->recordAudit('wallet.update', $wallet, $request, $data);

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
        $this->recordAudit('network.store', null, $request, $data);

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
        $this->recordAudit('fee.update', $fee, $request, $data);

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

    private function recordAudit(string $action, ?object $model, Request $request, array $payload = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $model ? $model::class : null,
            'auditable_id' => $model?->id,
            'payload' => $payload + ['user_agent' => $request->userAgent()],
            'ip' => $request->ip(),
        ]);
    }

    private function sections(): array
    {
        return [
            'overview' => ['label' => 'الرئيسية', 'icon' => '▦', 'group' => 'المراقبة'],
            'transfers' => ['label' => 'التحويلات', 'icon' => '↔', 'group' => 'الإدارة'],
            'users' => ['label' => 'المستخدمون و KYC', 'icon' => '◌', 'group' => 'الإدارة'],
            'wallets' => ['label' => 'المحافظ', 'icon' => '▤', 'group' => 'الإدارة'],
            'currencies' => ['label' => 'العملات والرسوم', 'icon' => '$', 'group' => 'الإدارة'],
            'orders' => ['label' => 'الطلبات', 'icon' => '□', 'group' => 'الإدارة'],
            'notifications' => ['label' => 'الإشعارات', 'icon' => '◉', 'group' => 'النظام'],
            'reports' => ['label' => 'التقارير', 'icon' => '↟', 'group' => 'المراقبة'],
            'audit' => ['label' => 'سجل العمليات', 'icon' => '⌁', 'group' => 'المراقبة'],
            'roles' => ['label' => 'الصلاحيات', 'icon' => '◇', 'group' => 'النظام'],
            'security' => ['label' => 'الأمان', 'icon' => '⎋', 'group' => 'النظام'],
            'settings' => ['label' => 'الإعدادات', 'icon' => '⚙', 'group' => 'النظام'],
            'support' => ['label' => 'الدعم الفني', 'icon' => '?', 'group' => 'النظام'],
        ];
    }
}
