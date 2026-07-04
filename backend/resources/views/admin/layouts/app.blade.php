@php
    $pageTitle = $sections[$page]['label'] ?? 'لوحة التحكم';
    $sectionUrl = fn ($key) => $key === 'overview'
        ? route('control.dashboard', [], false)
        : route('control.section', ['section' => $key], false);
    $adminCssPath = public_path('css/admin.css');
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USDT STORE - {{ $pageTitle }}</title>
    <link rel="stylesheet" href="/css/admin.css?v={{ is_file($adminCssPath) ? filemtime($adminCssPath) : time() }}">
    @if (is_file($adminCssPath))
        <style>{!! file_get_contents($adminCssPath) !!}</style>
    @endif
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
