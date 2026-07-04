<form class="toolbar" method="get" action="{{ $sectionUrl($page) }}">
    <input name="q" value="{{ $filters['q'] }}" placeholder="بحث: مستخدم، بريد، رقم عملية، TXID، عنوان محفظة">
    <select name="status">
        <option value="">كل حالات التحويل</option>
        @foreach ($statusLabels as $key => $label)
            <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn" type="submit">بحث وتصفية</button>
</form>
