@extends('admin.layouts.app')

@section('subtitle', 'ملخص سريع للأرصدة، المستخدمين، التحويلات، الأرباح، وآخر العمليات.')

@section('content')
@php
    $maxDaily = max((float) $dailyMovement->max('amount'), 1);
@endphp

@include('admin.partials.filters')

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
        <div class="panel-head">
            <div><h2>آخر العمليات</h2><small>مختصر آخر التحويلات</small></div>
            <a class="btn secondary" href="{{ route('control.section', ['section' => 'transfers'], false) }}">فتح التحويلات</a>
        </div>
        <div class="panel-body summary-list">
            @forelse ($transactions->take(6) as $tx)
                <div class="summary-row">
                    <span>#{{ $tx->id }} - {{ $tx->user?->email ?? '-' }}</span>
                    <span class="badge {{ $statusClass[$tx->status] ?? '' }}">{{ $statusLabels[$tx->status] ?? $tx->status }}</span>
                    <b>{{ number_format((float) $tx->amount, 2) }}</b>
                </div>
            @empty
                <div class="summary-row"><span>لا توجد عمليات حديثة.</span></div>
            @endforelse
        </div>
    </div>
</section>
@endsection
