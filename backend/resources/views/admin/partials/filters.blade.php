@php
    $filterAction = $page === 'overview'
        ? route('control.dashboard', [], false)
        : route('control.section', ['section' => $page], false);
@endphp

<form class="toolbar" method="get" action="{{ $filterAction }}">
    <input name="q" value="{{ $filters['q'] }}" placeholder="بحث: مستخدم، بريد، رقم عملية، TXID، عنوان محفظة">
    <select name="status">
        <option value="">كل حالات التحويل</option>
        @foreach ($statusLabels as $key => $label)
            <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn" type="submit">بحث وتصفية</button>
</form>
