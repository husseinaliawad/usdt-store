@extends('admin.layouts.app')

@section('subtitle', 'إنشاء محافظ المستخدمين، تحديث العناوين والأرصدة، ومراقبة المحافظ.')

@section('content')
<section class="panel">
    <div class="panel-head"><div><h2>إدارة المحافظ</h2><small>إنشاء محفظة، تحديث العنوان والرصيد، ومراقبة الأرصدة</small></div></div>
    <form class="form-grid" method="post" action="{{ route('control.wallets.store', [], false) }}">
        @csrf
        <select name="user_id" required>@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->email }}</option>@endforeach</select>
        <select name="network_id" required>@foreach ($networks as $network)<option value="{{ $network->id }}">{{ $network->code }}</option>@endforeach</select>
        <input name="address" placeholder="عنوان المحفظة أو الحساب" required>
        <input name="balance" placeholder="الرصيد" type="number" step="0.000001" required>
        <button class="btn" type="submit">إنشاء / تحديث</button>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>المستخدم</th><th>الشبكة / النوع</th><th>العنوان</th><th>الرصيد</th><th>أساسية</th><th>آخر تحديث</th><th>حفظ</th></tr></thead>
            <tbody>
            @foreach ($wallets as $wallet)
                <tr>
                    <form method="post" action="{{ route('control.wallets.update', $wallet, false) }}">
                        @csrf
                        <td>{{ $wallet->user?->email }}</td>
                        <td><span class="badge">{{ $wallet->network?->code }}</span></td>
                        <td><input name="address" value="{{ $wallet->address }}" required></td>
                        <td><input name="balance" type="number" step="0.000001" value="{{ $wallet->balance }}" required></td>
                        <td><input type="checkbox" name="is_primary" value="1" @checked($wallet->is_primary)></td>
                        <td>{{ $wallet->updated_at?->format('Y-m-d H:i') }}</td>
                        <td><button class="btn secondary" type="submit">حفظ</button></td>
                    </form>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
