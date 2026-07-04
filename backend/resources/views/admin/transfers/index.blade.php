@extends('admin.layouts.app')

@section('subtitle', 'جميع التحويلات، قيد المراجعة، قيد التنفيذ، مكتملة، مرفوضة، وتعديل الحالة.')

@section('content')
@include('admin.partials.filters')

<section class="panel">
    <div class="panel-head">
        <div><h2>التحويلات</h2><small>جميع التحويلات وتعديل الحالة وإثبات الدفع</small></div>
        <span class="badge">{{ $transactions->count() }} عملية</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>المستخدم</th><th>النوع</th><th>الشبكة</th><th>المبلغ</th><th>الرسوم</th><th>الحالة</th><th>إثبات / TXID</th><th>التاريخ</th><th>تعديل الحالة</th></tr>
            </thead>
            <tbody>
            @forelse ($transactions as $tx)
                <tr>
                    <td>{{ $tx->id }}</td>
                    <td>{{ $tx->user?->email ?? '-' }}</td>
                    <td><span class="badge">{{ $typeLabels[$tx->type] ?? $tx->type }}</span></td>
                    <td>{{ $tx->network?->code ?? '-' }}</td>
                    <td>{{ number_format((float) $tx->amount, 2) }} USDT</td>
                    <td>{{ number_format((float) $tx->fee, 2) }}</td>
                    <td><span class="badge {{ $statusClass[$tx->status] ?? '' }}">{{ $statusLabels[$tx->status] ?? $tx->status }}</span></td>
                    <td>
                        @if ($tx->proof_path)
                            <a class="badge green" href="{{ asset('storage/'.$tx->proof_path) }}" target="_blank">عرض الإثبات</a>
                        @else
                            <span class="muted">لا يوجد</span>
                        @endif
                        <div class="muted">{{ $tx->txid ?: '-' }}</div>
                    </td>
                    <td>{{ $tx->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <form class="compact-form" method="post" action="{{ route('control.transactions.status', $tx, false) }}">
                            @csrf
                            <select name="status">
                                @foreach ($statusLabels as $key => $label)
                                    <option value="{{ $key }}" @selected($tx->status === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input name="txid" value="{{ $tx->txid }}" placeholder="TXID">
                            <input name="note" value="{{ $tx->note }}" placeholder="ملاحظة">
                            <button class="btn secondary" type="submit">حفظ</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10">لا توجد عمليات مطابقة للفلاتر.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
