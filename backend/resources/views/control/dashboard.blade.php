<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USDT STORE - {{ $sections[$page]['label'] ?? 'لوحة التحكم' }}</title>
    <style>
        :root {
            --bg: #0a0b0d;
            --surface: #121417;
            --surface-2: #191d22;
            --surface-3: #20262d;
            --line: rgba(255,255,255,.10);
            --text: #f4f7f9;
            --muted: #97a3ad;
            --gold: #d7a84a;
            --gold-soft: rgba(215,168,74,.14);
            --green: #40c779;
            --green-soft: rgba(64,199,121,.14);
            --red: #ff6b6b;
            --red-soft: rgba(255,107,107,.14);
            --blue-soft: rgba(101,168,255,.14);
            --purple-soft: rgba(181,140,255,.14);
            font-family: Tahoma, Arial, sans-serif;
        }

        * { box-sizing: border-box; }
        body { margin: 0; color: var(--text); background: linear-gradient(180deg, #0f1115 0, var(--bg) 420px); }
        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }
        .shell { display: grid; grid-template-columns: 288px minmax(0, 1fr); min-height: 100vh; }
        .sidebar { position: sticky; top: 0; height: 100vh; overflow-y: auto; padding: 20px 16px; border-left: 1px solid var(--line); background: rgba(10,11,13,.94); }
        .brand { display: flex; align-items: center; gap: 12px; padding: 6px 8px 18px; border-bottom: 1px solid var(--line); margin-bottom: 14px; }
        .mark { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 12px; color: #111; font-weight: 900; background: linear-gradient(135deg, #f4d27b, var(--gold)); }
        .brand strong { display: block; font-size: 15px; }
        .brand span { display: block; color: var(--muted); font-size: 12px; margin-top: 3px; }
        .nav-group { margin-top: 14px; }
        .nav-title { color: var(--muted); font-size: 11px; font-weight: 700; padding: 10px 10px 6px; }
        .nav a, .logout { display: flex; align-items: center; gap: 10px; width: 100%; min-height: 42px; padding: 0 10px; border: 0; border-radius: 8px; background: transparent; color: #c7d0d8; cursor: pointer; text-align: right; }
        .nav a:hover, .nav a.active, .logout:hover { color: var(--text); background: var(--surface-2); }
        .nav-icon { width: 26px; height: 26px; display: grid; place-items: center; border: 1px solid var(--line); border-radius: 7px; color: var(--gold); background: rgba(255,255,255,.04); flex: 0 0 auto; }
        .content { min-width: 0; padding: 24px; }
        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; }
        h1 { margin: 0; font-size: clamp(24px, 3vw, 34px); letter-spacing: 0; }
        .subtitle { margin-top: 8px; color: var(--muted); line-height: 1.7; }
        .admin-chip { display: inline-flex; align-items: center; min-height: 40px; padding: 0 13px; border: 1px solid var(--line); border-radius: 8px; background: var(--surface); white-space: nowrap; }
        .notice { margin: 0 0 18px; padding: 12px 14px; border: 1px solid rgba(64,199,121,.26); border-radius: 8px; background: var(--green-soft); color: #a8f2c5; }
        .toolbar { display: grid; grid-template-columns: minmax(240px, 1fr) 180px auto; gap: 10px; margin-bottom: 18px; }
        input, select, textarea { width: 100%; min-height: 40px; border: 1px solid var(--line); border-radius: 8px; background: #0d0f12; color: var(--text); padding: 0 11px; outline: none; }
        textarea { min-height: 74px; padding-top: 10px; resize: vertical; }
        select option { color: #111; }
        input:focus, select:focus, textarea:focus { border-color: rgba(215,168,74,.62); }
        input[type="checkbox"] { width: auto; min-height: auto; margin-left: 6px; }
        .btn { min-height: 40px; border: 0; border-radius: 8px; padding: 0 13px; cursor: pointer; color: #101114; font-weight: 800; background: linear-gradient(90deg, #f3d17d, var(--gold)); }
        .btn.secondary { color: var(--text); border: 1px solid var(--line); background: var(--surface-3); }
        .kpi-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; }
        .kpi, .panel, .module { border: 1px solid var(--line); border-radius: 8px; background: linear-gradient(180deg, rgba(25,29,34,.98), rgba(16,18,21,.98)); box-shadow: 0 18px 42px rgba(0,0,0,.26); }
        .kpi { padding: 15px; min-height: 116px; }
        .kpi span { color: var(--muted); font-size: 12px; }
        .kpi strong { display: block; margin-top: 13px; font-size: 22px; color: var(--text); line-height: 1.35; }
        .kpi small { display: block; margin-top: 8px; color: var(--gold); }
        .panel { margin-top: 14px; overflow: hidden; }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 15px 16px; border-bottom: 1px solid var(--line); }
        .panel h2, .module h3 { margin: 0; font-size: 17px; }
        .panel small, .module small { display: block; margin-top: 5px; color: var(--muted); line-height: 1.6; }
        .panel-body { padding: 16px; }
        .two-col { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr); gap: 14px; }
        .chart { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); align-items: end; gap: 10px; min-height: 220px; }
        .bar-item { display: grid; gap: 8px; align-content: end; min-width: 0; }
        .bar { height: var(--h); min-height: 8px; border-radius: 7px 7px 3px 3px; background: linear-gradient(180deg, #f0cc75, #6cbf91); }
        .bar-label, .bar-value { color: var(--muted); font-size: 11px; text-align: center; white-space: nowrap; }
        .bar-value { color: #dfe7ee; }
        .summary-list, .module ul { display: grid; gap: 10px; }
        .summary-row, .module li { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 11px 12px; border: 1px solid var(--line); border-radius: 8px; background: rgba(255,255,255,.035); }
        .module { padding: 15px; }
        .module ul { margin: 13px 0 0; padding: 0; list-style: none; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 980px; border-collapse: collapse; }
        th, td { padding: 12px 14px; text-align: right; border-bottom: 1px solid var(--line); vertical-align: middle; }
        th { color: var(--muted); font-size: 12px; font-weight: 800; }
        td { color: #e5ebf0; font-size: 13px; }
        .badge { display: inline-flex; align-items: center; min-height: 26px; padding: 0 9px; border-radius: 999px; font-size: 12px; font-weight: 800; color: #f6dba0; background: var(--gold-soft); white-space: nowrap; }
        .badge.green { color: #9ff0bf; background: var(--green-soft); }
        .badge.red { color: #ffc1c1; background: var(--red-soft); }
        .badge.blue { color: #c8ddff; background: var(--blue-soft); }
        .badge.purple { color: #dfd1ff; background: var(--purple-soft); }
        .muted { color: var(--muted); }
        .compact-form { display: grid; grid-template-columns: 132px 140px minmax(150px, 1fr) auto; gap: 8px; align-items: center; }
        .form-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 10px; padding: 16px; border-bottom: 1px solid var(--line); }
        .section-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        @media (max-width: 1180px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; }
            .nav { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px; }
            .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .two-col, .section-grid { grid-template-columns: 1fr; }
            .form-grid, .toolbar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 680px) {
            .content { padding: 16px; }
            .topbar { flex-direction: column; }
            .nav, .kpi-grid, .form-grid, .toolbar, .compact-form { grid-template-columns: 1fr; }
            .bar-value { display: none; }
        }
    </style>
</head>
<body>
@php
    $statusLabels = ['pending' => 'قيد المراجعة', 'approved' => 'قيد التنفيذ', 'completed' => 'مكتملة', 'rejected' => 'مرفوضة', 'failed' => 'ملغاة / فاشلة'];
    $typeLabels = ['send' => 'إرسال', 'receive' => 'استلام', 'deposit' => 'إيداع', 'withdraw' => 'سحب'];
    $statusClass = ['pending' => 'blue', 'approved' => 'purple', 'completed' => 'green', 'rejected' => 'red', 'failed' => 'red'];
    $maxDaily = max((float) $dailyMovement->max('amount'), 1);
    $groups = collect($sections)->groupBy('group');
    $pageTitle = $sections[$page]['label'] ?? 'لوحة التحكم';
    $sectionRoute = fn ($key) => $key === 'overview' ? route('control.dashboard', [], false) : route('control.section', $key, false);
@endphp

<div class="shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="mark">T</div>
            <div>
                <strong>USDT STORE</strong>
                <span>مركز الإدارة والتشغيل</span>
            </div>
        </div>

        <nav class="nav">
            @foreach ($groups as $group => $items)
                <div class="nav-group">
                    <div class="nav-title">{{ $group }}</div>
                    @foreach ($items as $key => $item)
                        <a class="{{ $page === $key ? 'active' : '' }}" href="{{ $sectionRoute($key) }}">
                            <span class="nav-icon">{{ $item['icon'] }}</span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            @endforeach
            <div class="nav-group">
                <form method="post" action="{{ route('control.logout', [], false) }}">
                    @csrf
                    <button class="logout" type="submit"><span class="nav-icon">×</span> تسجيل الخروج</button>
                </form>
            </div>
        </nav>
    </aside>

    <main class="content">
        <section class="topbar">
            <div>
                <h1>{{ $pageTitle }}</h1>
                <div class="subtitle">كل قسم صار صفحة مستقلة لتسهيل الإدارة وتقليل التمرير داخل لوحة التحكم.</div>
            </div>
            <div class="admin-chip">مدير النظام: {{ auth()->user()->name }}</div>
        </section>

        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        @if (in_array($page, ['overview', 'transfers', 'users']))
            <form class="toolbar" method="get" action="{{ $sectionRoute($page) }}">
                <input name="q" value="{{ $filters['q'] }}" placeholder="بحث: مستخدم، بريد، رقم عملية، TXID، عنوان محفظة">
                <select name="status">
                    <option value="">كل حالات التحويل</option>
                    @foreach ($statusLabels as $key => $label)
                        <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn" type="submit">بحث وتصفية</button>
            </form>
        @endif

        @if ($page === 'overview')
            <section class="kpi-grid">
                <div class="kpi"><span>إجمالي الرصيد</span><strong>{{ number_format((float) $stats['balance'], 2) }} USDT</strong><small>{{ number_format($stats['activeWallets']) }} محفظة</small></div>
                <div class="kpi"><span>عدد المستخدمين</span><strong>{{ number_format($stats['users']) }}</strong><small>{{ number_format($stats['kycPending']) }} KYC قيد المراجعة</small></div>
                <div class="kpi"><span>تحويلات اليوم</span><strong>{{ number_format($stats['todayTransfers']) }}</strong><small>حركة آخر 24 ساعة</small></div>
                <div class="kpi"><span>التحويلات المعلقة</span><strong>{{ number_format($stats['pendingTransfers']) }}</strong><small>تحتاج مراجعة</small></div>
                <div class="kpi"><span>التحويلات المكتملة</span><strong>{{ number_format($stats['completedTransfers']) }}</strong><small>عمليات مغلقة</small></div>
                <div class="kpi"><span>إجمالي الأرباح</span><strong>{{ number_format((float) $stats['profits'], 2) }} USDT</strong><small>من العمليات المقبولة والمكتملة</small></div>
                <div class="kpi"><span>رسوم التحويل المحصلة</span><strong>{{ number_format((float) $stats['collectedFees'], 2) }} USDT</strong><small>كل الرسوم المسجلة</small></div>
                <div class="kpi"><span>إشعارات النظام</span><strong>{{ number_format($stats['notifications']) }}</strong><small>داخل التطبيق</small></div>
                <div class="kpi"><span>قيد التنفيذ</span><strong>{{ number_format($statusCounts['approved'] ?? 0) }}</strong><small>عمليات موافق عليها</small></div>
                <div class="kpi"><span>مرفوضة أو ملغاة</span><strong>{{ number_format(($statusCounts['rejected'] ?? 0) + ($statusCounts['failed'] ?? 0)) }}</strong><small>تحتاج متابعة</small></div>
            </section>

            <section class="two-col" style="margin-top:14px">
                <div class="panel">
                    <div class="panel-head"><div><h2>الحركة اليومية</h2><small>حجم العمليات خلال آخر 7 أيام</small></div></div>
                    <div class="panel-body">
                        <div class="chart">
                            @foreach ($dailyMovement as $day)
                                <div class="bar-item">
                                    <div class="bar-value">{{ number_format($day['amount'], 0) }}</div>
                                    <div class="bar" style="--h: {{ 16 + (($day['amount'] / $maxDaily) * 170) }}px"></div>
                                    <div class="bar-label">{{ $day['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-head"><div><h2>آخر العمليات</h2><small>مختصر آخر التحويلات</small></div><a class="btn secondary" href="{{ route('control.section', 'transfers', false) }}">فتح التحويلات</a></div>
                    <div class="panel-body summary-list">
                        @foreach ($transactions->take(6) as $tx)
                            <div class="summary-row"><span>#{{ $tx->id }} - {{ $tx->user?->email ?? '-' }}</span><span class="badge {{ $statusClass[$tx->status] ?? '' }}">{{ $statusLabels[$tx->status] ?? $tx->status }}</span><b>{{ number_format((float) $tx->amount, 2) }}</b></div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($page === 'transfers')
            <section class="panel">
                <div class="panel-head"><div><h2>التحويلات</h2><small>جميع التحويلات وتعديل الحالة وإثبات الدفع</small></div><span class="badge">{{ $transactions->count() }} عملية</span></div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>#</th><th>المستخدم</th><th>النوع</th><th>الشبكة</th><th>المبلغ</th><th>الرسوم</th><th>الحالة</th><th>إثبات / TXID</th><th>التاريخ</th><th>تعديل الحالة</th></tr></thead>
                        <tbody>
                        @forelse ($transactions as $tx)
                            <tr>
                                <td>{{ $tx->id }}</td><td>{{ $tx->user?->email ?? '-' }}</td><td><span class="badge">{{ $typeLabels[$tx->type] ?? $tx->type }}</span></td><td>{{ $tx->network?->code ?? '-' }}</td><td>{{ number_format((float) $tx->amount, 2) }} USDT</td><td>{{ number_format((float) $tx->fee, 2) }}</td>
                                <td><span class="badge {{ $statusClass[$tx->status] ?? '' }}">{{ $statusLabels[$tx->status] ?? $tx->status }}</span></td>
                                <td>@if ($tx->proof_path)<a class="badge green" href="{{ asset('storage/'.$tx->proof_path) }}" target="_blank">عرض الإثبات</a>@else<span class="muted">لا يوجد</span>@endif<div class="muted">{{ $tx->txid ?: '-' }}</div></td>
                                <td>{{ $tx->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <form class="compact-form" method="post" action="{{ route('control.transactions.status', $tx, false) }}">
                                        @csrf
                                        <select name="status">@foreach ($statusLabels as $key => $label)<option value="{{ $key }}" @selected($tx->status === $key)>{{ $label }}</option>@endforeach</select>
                                        <input name="txid" value="{{ $tx->txid }}" placeholder="TXID">
                                        <input name="note" value="{{ $tx->note }}" placeholder="ملاحظة">
                                        <button class="btn secondary" type="submit">حفظ</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10">لا توجد عمليات مطابقة للفلاتر.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($page === 'users')
            <section class="panel">
                <div class="panel-head"><div><h2>إدارة المستخدمين</h2><small>تعديل الصلاحية، تفعيل الحساب، KYC، المحافظ وسجل النشاط</small></div></div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>الاسم</th><th>البريد</th><th>الهاتف</th><th>المحافظ</th><th>العمليات</th><th>الصلاحية</th><th>KYC</th><th>الحساب</th><th>حفظ</th></tr></thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <form method="post" action="{{ route('control.users.update', $user, false) }}">
                                    @csrf
                                    <td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->phone }}</td><td><span class="badge blue">{{ $user->wallets_count }}</span></td><td><span class="badge purple">{{ $user->transactions_count }}</span></td>
                                    <td><select name="role"><option value="user" @selected($user->role === 'user')>User</option><option value="admin" @selected($user->role === 'admin')>Admin</option></select></td>
                                    <td><select name="kyc_status"><option value="not_submitted" @selected($user->kyc_status === 'not_submitted')>غير مقدم</option><option value="pending" @selected($user->kyc_status === 'pending')>قيد المراجعة</option><option value="approved" @selected($user->kyc_status === 'approved')>موثق</option><option value="rejected" @selected($user->kyc_status === 'rejected')>مرفوض</option></select></td>
                                    <td><label><input type="checkbox" name="is_active" value="1" @checked($user->is_active)> فعال</label></td>
                                    <td><button class="btn secondary" type="submit">حفظ</button></td>
                                </form>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="panel">
                <div class="panel-head"><div><h2>طلبات KYC</h2><small>طلبات التوثيق المرتبطة بالمستخدمين</small></div></div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>المستخدم</th><th>الاسم الكامل</th><th>الهاتف</th><th>الحالة</th><th>ملاحظة الإدارة</th><th>آخر تحديث</th></tr></thead>
                        <tbody>
                        @forelse ($kycVerifications as $kyc)
                            <tr><td>{{ $kyc->user?->email ?? '-' }}</td><td>{{ $kyc->full_name }}</td><td>{{ $kyc->phone }}</td><td><span class="badge {{ $statusClass[$kyc->status] ?? 'blue' }}">{{ $statusLabels[$kyc->status] ?? $kyc->status }}</span></td><td>{{ $kyc->admin_note ?: '-' }}</td><td>{{ $kyc->updated_at?->format('Y-m-d H:i') }}</td></tr>
                        @empty
                            <tr><td colspan="6">لا توجد طلبات KYC حالياً.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($page === 'wallets')
            <section class="panel">
                <div class="panel-head"><div><h2>إدارة المحافظ</h2><small>إنشاء محفظة، تحديث العنوان والرصيد، ومراقبة الأرصدة</small></div></div>
                <form class="form-grid" method="post" action="{{ route('control.wallets.store', [], false) }}">
                    @csrf
                    <select name="user_id" required>@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->email }}</option>@endforeach</select>
                    <select name="network_id" required>@foreach ($networks as $network)<option value="{{ $network->id }}">{{ $network->code }}</option>@endforeach</select>
                    <input name="address" placeholder="عنوان المحفظة أو الحساب" required>
                    <input name="balance" placeholder="الرصيد" type="number" step="0.000001" required>
                    <button class="btn" type="submit">إنشاء / تحديث</button>
                </form>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>المستخدم</th><th>الشبكة / النوع</th><th>العنوان</th><th>الرصيد</th><th>أساسية</th><th>آخر تحديث</th><th>حفظ</th></tr></thead>
                        <tbody>
                        @foreach ($wallets as $wallet)
                            <tr>
                                <form method="post" action="{{ route('control.wallets.update', $wallet, false) }}">
                                    @csrf
                                    <td>{{ $wallet->user?->email }}</td><td><span class="badge">{{ $wallet->network?->code }}</span></td><td><input name="address" value="{{ $wallet->address }}" required></td><td><input name="balance" type="number" step="0.000001" value="{{ $wallet->balance }}" required></td><td><input type="checkbox" name="is_primary" value="1" @checked($wallet->is_primary)></td><td>{{ $wallet->updated_at?->format('Y-m-d H:i') }}</td><td><button class="btn secondary" type="submit">حفظ</button></td>
                                </form>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($page === 'currencies')
            <section class="two-col">
                <div class="panel">
                    <div class="panel-head"><div><h2>إدارة العملات والشبكات</h2><small>العملات المدعومة، التفعيل، ورسوم الشبكة</small></div></div>
                    <form class="form-grid" method="post" action="{{ route('control.networks.store', [], false) }}">
                        @csrf
                        <input name="name" placeholder="اسم الشبكة" required><input name="code" placeholder="الكود مثل TRC20" required><input name="withdraw_fee" type="number" step="0.000001" placeholder="رسم السحب" required><input name="send_fee_percent" type="number" step="0.0001" placeholder="نسبة الإرسال" required><button class="btn" type="submit">حفظ شبكة</button>
                    </form>
                    <div class="table-wrap"><table><thead><tr><th>الاسم</th><th>الكود</th><th>رسم السحب</th><th>نسبة الإرسال</th><th>الحالة</th></tr></thead><tbody>@foreach ($networks as $network)<tr><td>{{ $network->name }}</td><td><span class="badge">{{ $network->code }}</span></td><td>{{ $network->withdraw_fee }}</td><td>{{ $network->send_fee_percent }}%</td><td><span class="badge {{ $network->is_active ? 'green' : 'red' }}">{{ $network->is_active ? 'فعالة' : 'معطلة' }}</span></td></tr>@endforeach</tbody></table></div>
                </div>
                <div class="panel">
                    <div class="panel-head"><div><h2>إدارة الرسوم</h2><small>رسوم ثابتة ونسبية حسب نوع التحويل</small></div></div>
                    <div class="table-wrap"><table><thead><tr><th>العمولة</th><th>النوع</th><th>ثابت</th><th>نسبة</th><th>فعال</th><th>حفظ</th></tr></thead><tbody>@foreach ($fees as $fee)<tr><form method="post" action="{{ route('control.fees.update', $fee, false) }}">@csrf<td>{{ $fee->name }}</td><td><span class="badge">{{ $typeLabels[$fee->type] ?? $fee->type }}</span></td><td><input name="fixed_fee" type="number" step="0.000001" value="{{ $fee->fixed_fee }}" required></td><td><input name="percent_fee" type="number" step="0.0001" value="{{ $fee->percent_fee }}" required></td><td><input type="checkbox" name="is_active" value="1" @checked($fee->is_active)></td><td><button class="btn secondary" type="submit">حفظ</button></td></form></tr>@endforeach</tbody></table></div>
                </div>
            </section>
        @endif

        @if ($page === 'orders')
            <section class="section-grid">
                <div class="module"><h3>الطلبات الجديدة</h3><ul><li><span>قيد المراجعة</span><b>{{ number_format($statusCounts['pending'] ?? 0) }}</b></li><li><span>قيد التنفيذ</span><b>{{ number_format($statusCounts['approved'] ?? 0) }}</b></li></ul></div>
                <div class="module"><h3>طلبات الإيداع والسحب</h3><ul><li><span>الإيداع</span><b>{{ number_format($typeCounts['deposit'] ?? 0) }}</b></li><li><span>السحب</span><b>{{ number_format($typeCounts['withdraw'] ?? 0) }}</b></li></ul></div>
                <div class="module"><h3>طلبات الدعم</h3><ul><li><span>نظام التذاكر</span><b>قيد الربط</b></li><li><span>متابعة الطلبات</span><b>من صفحة التحويلات</b></li></ul></div>
            </section>
        @endif

        @if ($page === 'notifications')
            <section class="panel">
                <div class="panel-head"><div><h2>الإشعارات</h2><small>إشعارات داخل التطبيق، البريد الإلكتروني، الرسائل النصية، والإشعارات الجماعية</small></div></div>
                <div class="table-wrap"><table><thead><tr><th>المستلم</th><th>العنوان</th><th>النص</th><th>الحالة</th><th>التاريخ</th></tr></thead><tbody>@forelse ($notifications as $notification)<tr><td>{{ $notification->user?->email ?? 'جماعي' }}</td><td>{{ $notification->title }}</td><td>{{ $notification->body }}</td><td><span class="badge {{ $notification->read_at ? 'green' : 'blue' }}">{{ $notification->read_at ? 'مقروء' : 'غير مقروء' }}</span></td><td>{{ $notification->created_at?->format('Y-m-d H:i') }}</td></tr>@empty<tr><td colspan="5">لا توجد إشعارات مسجلة.</td></tr>@endforelse</tbody></table></div>
            </section>
        @endif

        @if ($page === 'reports')
            <section class="two-col">
                <div class="panel"><div class="panel-head"><div><h2>التقارير الشهرية</h2><small>حجم التحويلات خلال آخر 6 أشهر</small></div></div><div class="panel-body summary-list">@foreach ($monthlyMovement as $month)<div class="summary-row"><span>{{ $month['label'] }}</span><span class="badge blue">{{ number_format($month['amount'], 2) }} USDT</span><span class="muted">{{ number_format($month['count']) }} عملية</span></div>@endforeach</div></div>
                <div class="section-grid" style="grid-template-columns:1fr">
                    <div class="module"><h3>الأرباح</h3><ul><li><span>إجمالي الأرباح</span><b>{{ number_format((float) $stats['profits'], 2) }} USDT</b></li><li><span>رسوم التحويل</span><b>{{ number_format((float) $stats['collectedFees'], 2) }} USDT</b></li></ul></div>
                    <div class="module"><h3>التصدير</h3><ul><li><span>Excel</span><b>قيد الربط</b></li><li><span>PDF</span><b>قيد الربط</b></li></ul></div>
                </div>
            </section>
        @endif

        @if ($page === 'audit')
            <section class="panel">
                <div class="panel-head"><div><h2>سجل العمليات</h2><small>من قام بالتعديل، وقت العملية، IP، والجهاز</small></div></div>
                <div class="table-wrap"><table><thead><tr><th>المدير</th><th>العملية</th><th>الهدف</th><th>IP</th><th>الجهاز</th><th>الوقت</th></tr></thead><tbody>@forelse ($auditLogs as $log)<tr><td>{{ $log->user?->email ?? '-' }}</td><td><span class="badge purple">{{ $log->action }}</span></td><td>{{ $log->auditable_type ? class_basename($log->auditable_type) : '-' }} #{{ $log->auditable_id ?? '-' }}</td><td>{{ $log->ip ?? '-' }}</td><td class="muted">{{ str($log->payload['user_agent'] ?? '-')->limit(70) }}</td><td>{{ $log->created_at?->format('Y-m-d H:i') }}</td></tr>@empty<tr><td colspan="6">سيظهر السجل بعد أول تعديل إداري.</td></tr>@endforelse</tbody></table></div>
            </section>
        @endif

        @if (in_array($page, ['roles', 'security', 'settings', 'support']))
            <section class="section-grid">
                @if ($page === 'roles')
                    <div class="module"><h3>Admin</h3><ul><li><span>الصلاحية</span><b>كامل</b></li></ul></div>
                    <div class="module"><h3>Support</h3><ul><li><span>الصلاحية</span><b>قيد الربط</b></li></ul></div>
                    <div class="module"><h3>Auditor</h3><ul><li><span>الصلاحية</span><b>قيد الربط</b></li></ul></div>
                @elseif ($page === 'security')
                    <div class="module"><h3>2FA</h3><ul><li><span>الحالة</span><b>قيد الربط</b></li></ul></div>
                    <div class="module"><h3>الجلسات</h3><ul><li><span>الإدارة</span><b>Laravel Sessions</b></li></ul></div>
                    <div class="module"><h3>قفل الحساب</h3><ul><li><span>الحالة</span><b>متاح من المستخدمين</b></li></ul></div>
                @elseif ($page === 'settings')
                    <div class="module"><h3>الشركة</h3><ul><li><span>الاسم</span><b>USDT STORE</b></li><li><span>الشعار</span><b>متاح بالملفات</b></li></ul></div>
                    <div class="module"><h3>اللغة والمنطقة</h3><ul><li><span>اللغة</span><b>العربية</b></li><li><span>المنطقة الزمنية</span><b>{{ config('app.timezone') }}</b></li></ul></div>
                    <div class="module"><h3>API والبريد</h3><ul><li><span>الإعدادات</span><b>ملفات env</b></li></ul></div>
                @else
                    <div class="module"><h3>نظام تذاكر</h3><ul><li><span>الحالة</span><b>قيد الربط</b></li></ul></div>
                    <div class="module"><h3>محادثة العملاء</h3><ul><li><span>الحالة</span><b>قيد الربط</b></li></ul></div>
                    <div class="module"><h3>متابعة الطلبات</h3><ul><li><span>المصدر</span><b>صفحة التحويلات</b></li></ul></div>
                @endif
            </section>
        @endif
    </main>
</div>
</body>
</html>
