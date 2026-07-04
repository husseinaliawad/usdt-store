@extends('admin.layouts.app')

@section('subtitle', 'إعدادات الأمان، المصادقة الثنائية، الجلسات، وقفل الحساب.')

@section('content')
<section class="section-grid">
    <div class="module">
        <h3>2FA</h3>
        <ul><li><span>الحالة</span><b>قيد الربط</b></li></ul>
    </div>
    <div class="module">
        <h3>الجلسات</h3>
        <ul><li><span>الإدارة</span><b>Laravel Sessions</b></li></ul>
    </div>
    <div class="module">
        <h3>قفل الحساب</h3>
        <ul><li><span>الحالة</span><b>متاح من المستخدمين</b></li></ul>
    </div>
</section>
@endsection
