@extends('admin.layouts.app')

@section('subtitle', 'عرض إشعارات التطبيق والإشعارات الجماعية وحالة القراءة.')

@section('content')
<section class="panel">
    <div class="panel-head"><div><h2>الإشعارات</h2><small>إشعارات داخل التطبيق، البريد الإلكتروني، الرسائل النصية، والإشعارات الجماعية</small></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>المستلم</th><th>العنوان</th><th>النص</th><th>الحالة</th><th>التاريخ</th></tr></thead>
            <tbody>
            @forelse ($notifications as $notification)
                <tr><td>{{ $notification->user?->email ?? 'جماعي' }}</td><td>{{ $notification->title }}</td><td>{{ $notification->body }}</td><td><span class="badge {{ $notification->read_at ? 'green' : 'blue' }}">{{ $notification->read_at ? 'مقروء' : 'غير مقروء' }}</span></td><td>{{ $notification->created_at?->format('Y-m-d H:i') }}</td></tr>
            @empty
                <tr><td colspan="5">لا توجد إشعارات مسجلة.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
