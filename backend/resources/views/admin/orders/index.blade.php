@extends('admin.layouts.app')

@section('subtitle', 'متابعة الطلبات الجديدة وطلبات الإيداع والسحب والدعم.')

@section('content')
<section class="section-grid">
    <div class="module">
        <h3>الطلبات الجديدة</h3>
        <ul>
            <li><span>قيد المراجعة</span><b>{{ number_format($statusCounts['pending'] ?? 0) }}</b></li>
            <li><span>قيد التنفيذ</span><b>{{ number_format($statusCounts['approved'] ?? 0) }}</b></li>
        </ul>
    </div>
    <div class="module">
        <h3>طلبات الإيداع والسحب</h3>
        <ul>
            <li><span>الإيداع</span><b>{{ number_format($typeCounts['deposit'] ?? 0) }}</b></li>
            <li><span>السحب</span><b>{{ number_format($typeCounts['withdraw'] ?? 0) }}</b></li>
        </ul>
    </div>
    <div class="module">
        <h3>طلبات الدعم</h3>
        <ul>
            <li><span>نظام التذاكر</span><b>قيد الربط</b></li>
            <li><span>متابعة الطلبات</span><b>من صفحة التحويلات</b></li>
        </ul>
    </div>
</section>
@endsection
