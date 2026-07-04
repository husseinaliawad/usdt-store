@extends('admin.layouts.app')

@section('subtitle', 'إدارة الشبكات المدعومة والرسوم الثابتة والنسبية حسب نوع التحويل.')

@section('content')
<section class="two-col">
    <div class="panel">
        <div class="panel-head"><div><h2>إدارة العملات والشبكات</h2><small>العملات المدعومة، التفعيل، ورسوم الشبكة</small></div></div>
        <form class="form-grid" method="post" action="{{ route('control.networks.store', [], false) }}">
            @csrf
            <input name="name" placeholder="اسم الشبكة" required>
            <input name="code" placeholder="الكود مثل TRC20" required>
            <input name="withdraw_fee" type="number" step="0.000001" placeholder="رسم السحب" required>
            <input name="send_fee_percent" type="number" step="0.0001" placeholder="نسبة الإرسال" required>
            <button class="btn" type="submit">حفظ شبكة</button>
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>الاسم</th><th>الكود</th><th>رسم السحب</th><th>نسبة الإرسال</th><th>الحالة</th></tr></thead>
                <tbody>
                @foreach ($networks as $network)
                    <tr><td>{{ $network->name }}</td><td><span class="badge">{{ $network->code }}</span></td><td>{{ $network->withdraw_fee }}</td><td>{{ $network->send_fee_percent }}%</td><td><span class="badge {{ $network->is_active ? 'green' : 'red' }}">{{ $network->is_active ? 'فعالة' : 'معطلة' }}</span></td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><div><h2>إدارة الرسوم</h2><small>رسوم ثابتة ونسبية حسب نوع التحويل</small></div></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>العمولة</th><th>النوع</th><th>ثابت</th><th>نسبة</th><th>فعال</th><th>حفظ</th></tr></thead>
                <tbody>
                @foreach ($fees as $fee)
                    <tr>
                        <form method="post" action="{{ route('control.fees.update', $fee, false) }}">
                            @csrf
                            <td>{{ $fee->name }}</td>
                            <td><span class="badge">{{ $typeLabels[$fee->type] ?? $fee->type }}</span></td>
                            <td><input name="fixed_fee" type="number" step="0.000001" value="{{ $fee->fixed_fee }}" required></td>
                            <td><input name="percent_fee" type="number" step="0.0001" value="{{ $fee->percent_fee }}" required></td>
                            <td><input type="checkbox" name="is_active" value="1" @checked($fee->is_active)></td>
                            <td><button class="btn secondary" type="submit">حفظ</button></td>
                        </form>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
