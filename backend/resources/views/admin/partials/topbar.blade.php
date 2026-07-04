<section class="topbar">
    <div>
        <h1>{{ $pageTitle }}</h1>
        <div class="subtitle">@yield('subtitle', 'كل قسم ضمن لوحة التحكم يفتح كصفحة مستقلة لتسهيل الإدارة وتقليل التمرير.')</div>
    </div>
    <div class="admin-chip">مدير النظام: {{ auth()->user()->name }}</div>
</section>

@if (session('status'))
    <div class="notice">{{ session('status') }}</div>
@endif
