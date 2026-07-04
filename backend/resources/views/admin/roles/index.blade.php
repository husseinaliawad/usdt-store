@extends('admin.layouts.app')

@section('subtitle', 'إدارة أدوار Admin و Support و Auditor والصلاحيات المخصصة.')

@section('content')
<section class="section-grid">
    <div class="module">
        <h3>Admin</h3>
        <ul><li><span>الصلاحية</span><b>كامل</b></li></ul>
    </div>
    <div class="module">
        <h3>Support</h3>
        <ul><li><span>الصلاحية</span><b>قيد الربط</b></li></ul>
    </div>
    <div class="module">
        <h3>Auditor</h3>
        <ul><li><span>الصلاحية</span><b>قيد الربط</b></li></ul>
    </div>
</section>
@endsection
