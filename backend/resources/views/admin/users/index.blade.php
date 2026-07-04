@extends('admin.layouts.app')

@section('subtitle', 'إدارة المستخدمين، تفعيل الحسابات، الصلاحيات، وطلبات توثيق الهوية.')

@section('content')
@include('admin.partials.filters')

<section class="panel">
    <div class="panel-head"><div><h2>إدارة المستخدمين</h2><small>تعديل الصلاحية، تفعيل الحساب، KYC، المحافظ وسجل النشاط</small></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>الاسم</th><th>البريد</th><th>الهاتف</th><th>المحافظ</th><th>العمليات</th><th>الصلاحية</th><th>KYC</th><th>الحساب</th><th>حفظ</th></tr></thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <form method="post" action="{{ route('control.users.update', $user, false) }}">
                        @csrf
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td><span class="badge blue">{{ $user->wallets_count }}</span></td>
                        <td><span class="badge purple">{{ $user->transactions_count }}</span></td>
                        <td><select name="role"><option value="user" @selected($user->role === 'user')>User</option><option value="admin" @selected($user->role === 'admin')>Admin</option></select></td>
                        <td><select name="kyc_status"><option value="not_submitted" @selected($user->kyc_status === 'not_submitted')>غير مقدم</option><option value="pending" @selected($user->kyc_status === 'pending')>قيد المراجعة</option><option value="approved" @selected($user->kyc_status === 'approved')>موثق</option><option value="rejected" @selected($user->kyc_status === 'rejected')>مرفوض</option></select></td>
                        <td><label><input type="checkbox" name="is_active" value="1" @checked($user->is_active)> فعال</label></td>
                        <td><button class="btn secondary" type="submit">حفظ</button></td>
                    </form>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-head"><div><h2>طلبات KYC</h2><small>طلبات التوثيق المرتبطة بالمستخدمين</small></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>المستخدم</th><th>الاسم الكامل</th><th>الهاتف</th><th>الحالة</th><th>ملاحظة الإدارة</th><th>آخر تحديث</th></tr></thead>
            <tbody>
            @forelse ($kycVerifications as $kyc)
                <tr><td>{{ $kyc->user?->email ?? '-' }}</td><td>{{ $kyc->full_name }}</td><td>{{ $kyc->phone }}</td><td><span class="badge {{ $statusClass[$kyc->status] ?? 'blue' }}">{{ $statusLabels[$kyc->status] ?? $kyc->status }}</span></td><td>{{ $kyc->admin_note ?: '-' }}</td><td>{{ $kyc->updated_at?->format('Y-m-d H:i') }}</td></tr>
            @empty
                <tr><td colspan="6">لا توجد طلبات KYC حالياً.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
