@extends('admin.layouts.app')

@section('subtitle', 'إعدادات الشركة، اللغة، المنطقة الزمنية، API، البريد والرسائل.')

@section('content')
<section class="section-grid">
    <div class="module">
        <h3>الشركة</h3>
        <ul>
            <li><span>الاسم</span><b>USDT STORE</b></li>
            <li><span>الشعار</span><b>متاح بالملفات</b></li>
        </ul>
    </div>
    <div class="module">
        <h3>اللغة والمنطقة</h3>
        <ul>
            <li><span>اللغة</span><b>العربية</b></li>
            <li><span>المنطقة الزمنية</span><b>{{ config('app.timezone') }}</b></li>
        </ul>
    </div>
    <div class="module">
        <h3>API والبريد</h3>
        <ul><li><span>الإعدادات</span><b>ملفات env</b></li></ul>
    </div>
</section>
@endsection
