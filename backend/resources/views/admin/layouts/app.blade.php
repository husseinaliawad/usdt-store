@php
    $statusLabels = [
        'pending' => 'قيد المراجعة',
        'approved' => 'قيد التنفيذ',
        'completed' => 'مكتملة',
        'rejected' => 'مرفوضة',
        'failed' => 'ملغاة / فاشلة',
    ];
    $typeLabels = [
        'send' => 'إرسال',
        'receive' => 'استلام',
        'deposit' => 'إيداع',
        'withdraw' => 'سحب',
    ];
    $statusClass = [
        'pending' => 'blue',
        'approved' => 'purple',
        'completed' => 'green',
        'rejected' => 'red',
        'failed' => 'red',
    ];
    $pageTitle = $sections[$page]['label'] ?? 'لوحة التحكم';
    $sectionUrl = fn ($key) => $key === 'overview'
        ? route('control.dashboard', [], false)
        : route('control.section', ['section' => $key], false);
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USDT STORE - {{ $pageTitle }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="shell">
    @include('admin.partials.sidebar')

    <main class="content">
        @include('admin.partials.topbar')

        @yield('content')
    </main>
</div>
</body>
</html>
