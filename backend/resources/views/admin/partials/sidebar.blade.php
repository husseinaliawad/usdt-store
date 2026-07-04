@php
    $groups = collect($sections)
        ->map(fn ($item, $key) => $item + ['key' => $key])
        ->groupBy('group');
@endphp

<aside class="sidebar">
    <div class="brand">
        <div class="mark">T</div>
        <div>
            <strong>USDT STORE</strong>
            <span>مركز الإدارة والتشغيل</span>
        </div>
    </div>

    <nav class="nav">
        @foreach ($groups as $group => $items)
            <div class="nav-group">
                <div class="nav-title">{{ $group }}</div>
                @foreach ($items as $item)
                    <a class="{{ $page === $item['key'] ? 'active' : '' }}" href="{{ $sectionUrl($item['key']) }}">
                        <span class="nav-icon">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        @endforeach

        <div class="nav-group">
            <form method="post" action="{{ route('control.logout', [], false) }}">
                @csrf
                <button class="logout" type="submit">
                    <span class="nav-icon">×</span>
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </nav>
</aside>
