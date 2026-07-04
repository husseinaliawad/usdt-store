@extends('admin.layouts.app')

@section('subtitle', 'تقارير الأرباح، حجم التداول، الرسوم، والتصدير المستقبلي.')

@section('content')
<section class="two-col">
    <div class="panel">
        <div class="panel-head"><div><h2>التقارير الشهرية</h2><small>حجم التحويلات خلال آخر 6 أشهر</small></div></div>
        <div class="panel-body summary-list">
            @foreach ($monthlyMovement as $month)
                <div class="summary-row">
                    <span>{{ $month['label'] }}</span>
                    <span class="badge blue">{{ number_format($month['amount'], 2) }} USDT</span>
                    <span class="muted">{{ number_format($month['count']) }} عملية</span>
                </div>
            @endforeach
        </div>
    </div>
    <div class="section-grid" style="grid-template-columns:1fr">
        <div class="module">
            <h3>الأرباح</h3>
            <ul>
                <li><span>إجمالي الأرباح</span><b>{{ number_format((float) $stats['profits'], 2) }} USDT</b></li>
                <li><span>رسوم التحويل</span><b>{{ number_format((float) $stats['collectedFees'], 2) }} USDT</b></li>
            </ul>
        </div>
        <div class="module">
            <h3>التصدير</h3>
            <ul>
                <li><span>Excel</span><b>قيد الربط</b></li>
                <li><span>PDF</span><b>قيد الربط</b></li>
            </ul>
        </div>
    </div>
</section>
@endsection
