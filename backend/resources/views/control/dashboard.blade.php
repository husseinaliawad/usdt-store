<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USDT STORE Control</title>
    <style>
        :root {
            --black: #070707;
            --panel: #11110f;
            --panel-2: #181611;
            --panel-3: #211d14;
            --gold: #dca83a;
            --gold-2: #f6d27a;
            --line: rgba(246, 210, 122, .20);
            --soft-line: rgba(255, 255, 255, .08);
            --text: #f7f2e8;
            --muted: #a8a093;
            --green: #65d685;
            --red: #ff7474;
            font-family: Arial, Tahoma, sans-serif;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background:
                radial-gradient(circle at top right, rgba(220, 168, 58, .16), transparent 32rem),
                linear-gradient(180deg, #0c0b09, var(--black) 36rem);
            color: var(--text);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }

        .shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 22px;
            border-left: 1px solid var(--soft-line);
            background: rgba(7, 7, 7, .74);
            backdrop-filter: blur(18px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--soft-line);
        }

        .mark {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #151006;
            font-size: 27px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--gold), var(--gold-2));
            box-shadow: 0 12px 34px rgba(220, 168, 58, .28);
        }

        .brand strong { display: block; font-size: 16px; }
        .brand span { display: block; margin-top: 3px; color: var(--muted); font-size: 12px; }

        .nav { display: grid; gap: 8px; margin-top: 24px; }
        .nav a, .logout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 44px;
            padding: 0 14px;
            border-radius: 14px;
            color: var(--muted);
            background: transparent;
            border: 0;
            cursor: pointer;
            width: 100%;
        }
        .nav a:hover, .logout:hover {
            color: var(--gold-2);
            background: rgba(246, 210, 122, .08);
        }

        .content { padding: 26px; min-width: 0; }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 { margin: 0; font-size: clamp(24px, 3vw, 36px); letter-spacing: 0; }
        .subtitle { color: var(--muted); margin-top: 8px; }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            color: var(--gold-2);
            background: rgba(246, 210, 122, .06);
            white-space: nowrap;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat, .panel {
            border: 1px solid var(--line);
            border-radius: 20px;
            background: linear-gradient(180deg, rgba(24, 22, 17, .94), rgba(8, 8, 8, .96));
            box-shadow: 0 18px 46px rgba(0, 0, 0, .34);
        }

        .stat { padding: 18px; min-height: 118px; }
        .stat span { color: var(--muted); font-size: 13px; }
        .stat strong { display: block; margin-top: 14px; font-size: 25px; color: var(--gold-2); }

        .panel { margin-top: 18px; overflow: hidden; }
        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--soft-line);
        }
        .panel h2 { margin: 0; font-size: 18px; }
        .panel small { color: var(--muted); }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th, td { padding: 14px 16px; text-align: right; border-bottom: 1px solid var(--soft-line); vertical-align: middle; }
        th { color: var(--muted); font-size: 12px; font-weight: 700; }
        td { font-size: 14px; }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(246, 210, 122, .10);
            color: var(--gold-2);
            font-size: 12px;
            font-weight: 700;
        }
        .ok { color: var(--green); background: rgba(101, 214, 133, .10); }
        .bad { color: var(--red); background: rgba(255, 116, 116, .10); }

        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .btn {
            border: 0;
            border-radius: 11px;
            padding: 9px 12px;
            cursor: pointer;
            color: #111;
            font-weight: 800;
            background: linear-gradient(90deg, var(--gold), var(--gold-2));
        }
        .btn.secondary {
            color: var(--text);
            background: rgba(255, 255, 255, .08);
            border: 1px solid var(--soft-line);
        }
        .btn.danger { color: #230808; background: linear-gradient(90deg, #ff8a8a, #ffb0b0); }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            padding: 16px 20px 4px;
        }
        input, select {
            width: 100%;
            min-height: 42px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: rgba(0, 0, 0, .28);
            color: var(--text);
            padding: 0 12px;
            outline: none;
        }
        select option { color: #111; }
        input:focus, select:focus { border-color: rgba(246, 210, 122, .55); }

        .notice {
            margin-bottom: 18px;
            padding: 13px 16px;
            border: 1px solid rgba(101, 214, 133, .24);
            border-radius: 14px;
            background: rgba(101, 214, 133, .08);
            color: var(--green);
        }

        @media (max-width: 1050px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; }
            .nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .content { padding: 18px; }
            .topbar { align-items: flex-start; flex-direction: column; }
            .grid, .nav, .form-grid { grid-template-columns: 1fr; }
            .sidebar { padding: 18px; }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="mark">T</div>
            <div>
                <strong>USDT STORE</strong>
                <span>Control Center</span>
            </div>
        </div>
        <nav class="nav">
            <a href="#overview">نظرة عامة <span>01</span></a>
            <a href="#transactions">العمليات <span>02</span></a>
            <a href="#users">المستخدمون <span>03</span></a>
            <a href="#wallets">المحافظ <span>04</span></a>
            <a href="#settings">الإعدادات <span>05</span></a>
            <form method="post" action="{{ route('control.logout') }}">
                @csrf
                <button class="logout" type="submit">تسجيل الخروج <span>←</span></button>
            </form>
        </nav>
    </aside>

    <main class="content">
        <section class="topbar" id="overview">
            <div>
                <h1>لوحة التحكم</h1>
                <div class="subtitle">إدارة العمليات، المحافظ، المستخدمين، والشبكات من مكان واحد.</div>
            </div>
            <div class="pill">مرحباً {{ auth()->user()->name }}</div>
        </section>

        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        <section class="grid">
            <div class="stat"><span>المستخدمون</span><strong>{{ number_format($stats['users']) }}</strong></div>
            <div class="stat"><span>عمليات معلقة</span><strong>{{ number_format($stats['pending']) }}</strong></div>
            <div class="stat"><span>أرصدة المحافظ</span><strong>{{ number_format((float) $stats['walletBalance'], 2) }} USDT</strong></div>
            <div class="stat"><span>رسوم اليوم</span><strong>{{ number_format((float) $stats['todayFees'], 2) }} USDT</strong></div>
        </section>

        <section class="panel" id="transactions">
            <div class="panel-head">
                <div><h2>العمليات الأخيرة</h2><small>قبول ورفض الإيداع والسحب والإرسال</small></div>
                <span class="badge">{{ $transactions->count() }} عملية</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>المستخدم</th><th>النوع</th><th>الشبكة</th><th>المبلغ</th><th>الحالة</th><th>التاريخ</th><th>إجراء</th></tr></thead>
                    <tbody>
                    @foreach ($transactions as $tx)
                        <tr>
                            <td>{{ $tx->id }}</td>
                            <td>{{ $tx->user?->email }}</td>
                            <td><span class="badge">{{ $tx->type }}</span></td>
                            <td>{{ $tx->network?->code ?? '-' }}</td>
                            <td>{{ number_format((float) $tx->amount, 2) }} USDT</td>
                            <td><span class="badge {{ $tx->status === 'completed' ? 'ok' : ($tx->status === 'rejected' ? 'bad' : '') }}">{{ $tx->status }}</span></td>
                            <td>{{ $tx->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                @if ($tx->status === 'pending')
                                    <div class="actions">
                                        <form method="post" action="{{ route('control.transactions.approve', $tx) }}">@csrf<button class="btn" type="submit">قبول</button></form>
                                        <form method="post" action="{{ route('control.transactions.reject', $tx) }}">@csrf<input name="note" placeholder="سبب الرفض" required style="width:130px"><button class="btn danger" type="submit">رفض</button></form>
                                    </div>
                                @else
                                    <span class="badge">تمت المراجعة</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel" id="users">
            <div class="panel-head"><div><h2>المستخدمون</h2><small>الصلاحيات، KYC، وتفعيل الحساب</small></div></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>الاسم</th><th>البريد</th><th>الهاتف</th><th>الصلاحية</th><th>KYC</th><th>فعال</th><th>حفظ</th></tr></thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <form method="post" action="{{ route('control.users.update', $user) }}">
                                @csrf
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td><select name="role"><option value="user" @selected($user->role === 'user')>مستخدم</option><option value="admin" @selected($user->role === 'admin')>مدير</option></select></td>
                                <td><select name="kyc_status"><option value="not_submitted" @selected($user->kyc_status === 'not_submitted')>غير مقدم</option><option value="pending" @selected($user->kyc_status === 'pending')>قيد المراجعة</option><option value="approved" @selected($user->kyc_status === 'approved')>مقبول</option><option value="rejected" @selected($user->kyc_status === 'rejected')>مرفوض</option></select></td>
                                <td><input type="checkbox" name="is_active" value="1" @checked($user->is_active)></td>
                                <td><button class="btn secondary" type="submit">حفظ</button></td>
                            </form>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel" id="wallets">
            <div class="panel-head"><div><h2>المحافظ</h2><small>إضافة وتعديل عناوين وأرصدة المستخدمين</small></div></div>
            <form class="form-grid" method="post" action="{{ route('control.wallets.store') }}">
                @csrf
                <select name="user_id" required>@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->email }}</option>@endforeach</select>
                <select name="network_id" required>@foreach ($networks as $network)<option value="{{ $network->id }}">{{ $network->code }}</option>@endforeach</select>
                <input name="address" placeholder="عنوان المحفظة" required>
                <input name="balance" placeholder="الرصيد" type="number" step="0.000001" required>
                <button class="btn" type="submit">إضافة / تحديث</button>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>المستخدم</th><th>الشبكة</th><th>العنوان</th><th>الرصيد</th><th>أساسية</th><th>حفظ</th></tr></thead>
                    <tbody>
                    @foreach ($wallets as $wallet)
                        <tr>
                            <form method="post" action="{{ route('control.wallets.update', $wallet) }}">
                                @csrf
                                <td>{{ $wallet->user?->email }}</td>
                                <td><span class="badge">{{ $wallet->network?->code }}</span></td>
                                <td><input name="address" value="{{ $wallet->address }}" required></td>
                                <td><input name="balance" type="number" step="0.000001" value="{{ $wallet->balance }}" required></td>
                                <td><input type="checkbox" name="is_primary" value="1" @checked($wallet->is_primary)></td>
                                <td><button class="btn secondary" type="submit">حفظ</button></td>
                            </form>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel" id="settings">
            <div class="panel-head"><div><h2>الشبكات والعمولات</h2><small>تحكم بالشبكات المدعومة ورسوم العمليات</small></div></div>
            <form class="form-grid" method="post" action="{{ route('control.networks.store') }}">
                @csrf
                <input name="name" placeholder="اسم الشبكة" required>
                <input name="code" placeholder="الكود" required>
                <input name="withdraw_fee" type="number" step="0.000001" placeholder="رسوم السحب" required>
                <input name="send_fee_percent" type="number" step="0.0001" placeholder="نسبة الإرسال" required>
                <button class="btn" type="submit">حفظ شبكة</button>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>العمولة</th><th>النوع</th><th>ثابت</th><th>نسبة</th><th>فعال</th><th>حفظ</th></tr></thead>
                    <tbody>
                    @foreach ($fees as $fee)
                        <tr>
                            <form method="post" action="{{ route('control.fees.update', $fee) }}">
                                @csrf
                                <td>{{ $fee->name }}</td>
                                <td><span class="badge">{{ $fee->type }}</span></td>
                                <td><input name="fixed_fee" type="number" step="0.000001" value="{{ $fee->fixed_fee }}" required></td>
                                <td><input name="percent_fee" type="number" step="0.0001" value="{{ $fee->percent_fee }}" required></td>
                                <td><input type="checkbox" name="is_active" value="1" @checked($fee->is_active)></td>
                                <td><button class="btn secondary" type="submit">حفظ</button></td>
                            </form>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
