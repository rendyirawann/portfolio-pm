<header class="section-head" data-reveal>
    <p class="kicker">{{ $kicker }}</p>
    <h2 class="section-title" id="{{ $id }}" @isset($key) data-pf="{{ $key }}_title" @endisset>{{ $title }}</h2>
    @if (! empty($subtitle) || isset($key))<p class="section-sub" @isset($key) data-pf="{{ $key }}_subtitle" @endisset>{{ $subtitle }}</p>@endif
</header>
