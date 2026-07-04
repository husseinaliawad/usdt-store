@extends('admin.layouts.app')

@section('subtitle', 'سجل التعديلات الإدارية مع المستخدم، الوقت، IP، والجهاز.')

@section('content')
<section class="panel">
    <div class="panel-head"><div><h2>سجل العمليات</h2><small>من قام بالتعديل، وقت العملية، IP، والجهاز</small></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>المدير</th><th>العملية</th><th>الهدف</th><th>IP</th><th>الجهاز</th><th>الوقت</th></tr></thead>
            <tbody>
            @forelse ($auditLogs as $log)
                <tr><td>{{ $log->user?->email ?? '-' }}</td><td><span class="badge purple">{{ $log->action }}</span></td><td>{{ $log->auditable_type ? class_basename($log->auditable_type) : '-' }} #{{ $log->auditable_id ?? '-' }}</td><td>{{ $log->ip ?? '-' }}</td><td class="muted">{{ str($log->payload['user_agent'] ?? '-')->limit(70) }}</td><td>{{ $log->created_at?->format('Y-m-d H:i') }}</td></tr>
            @empty
                <tr><td colspan="6">سيظهر السجل بعد أول تعديل إداري.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
