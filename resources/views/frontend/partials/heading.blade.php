@php
    // Two-tone title: the last word is dimmed (ref. chimox) — kept as one text node
    // for the admin live preview, which replaces textContent of [data-pf].
    $words = preg_split('/\s+/', trim((string) $title)) ?: [];
    $last = count($words) > 1 ? array_pop($words) : null;
@endphp
<header class="section-head" data-reveal>
    <div>
        <p class="kicker">{{ $kicker }}</p>
        <h2 class="section-title" id="{{ $id }}">
            <span @isset($key) data-pf="{{ $key }}_title" @endisset>{{ $last ? implode(' ', $words) : $title }}</span>@if ($last) <span class="section-title__dim">{{ $last }}</span>@endif
        </h2>
    </div>
    @if (! empty($subtitle) || isset($key))<p class="section-sub" @isset($key) data-pf="{{ $key }}_subtitle" @endisset>{{ $subtitle }}</p>@endif
</header>
