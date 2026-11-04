@php
    use App\Support\Portfolio\Media;
    use Illuminate\Support\Str;
    $c = $content;
    $nonce = app(\App\Http\Middleware\SecurityHeaders::NONCE);
    $brandLogo = $c['brand_logo'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    @include('frontend.partials.seo')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Archivo:wdth,wght@112.5,800;112.5,900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('assets/front/css/portfolio.css') }}?v={{ filemtime(public_path('assets/front/css/portfolio.css')) }}">
    @stack('head')
    {{-- Theme before first paint. Shares the admin's key so one choice applies everywhere; dark is the default. --}}
    <script nonce="{{ $nonce }}">(function(){var d=document.documentElement,m='dark';d.classList.replace('no-js','js');try{m=localStorage.getItem('data-bs-theme-mode')||localStorage.getItem('data-bs-theme')||'dark';}catch(e){}var t=m==='system'?(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'):(m==='light'?'light':'dark');d.setAttribute('data-theme',t);d.setAttribute('data-theme-mode',m);})();</script>
</head>
<body class="@yield('body_class')">
    <a class="skip-link" href="#main">Lewati ke konten</a>

    {{-- ================= NAVBAR: floating pill ================= --}}
    @php $link = fn ($x) => \App\Support\Portfolio\PortfolioData::href($x); @endphp
    <header class="nav" data-nav>
        <div class="nav__pill">
            <a href="{{ route('home') }}" class="nav__brand" aria-label="{{ $c['brand_name'] }} — beranda">
                @if ($brandLogo)
                    <img src="{{ Media::url($brandLogo) }}" alt="{{ $c['brand_name'] }}" height="28" width="96" class="nav__logo">
                @else
                    <img src="{{ $brand['logo_url'] }}" alt="" class="nav__mark" width="28" height="28">
                    <span class="nav__name" data-pf="brand_name">{{ $c['brand_name'] }}</span>
                @endif
            </a>

            <ul class="nav__links">
                @foreach ($nav->take(5) as $item)
                    <li><a href="{{ $link($item->target) }}" class="nav__link" @if (str_starts_with($item->target, 'http')) target="_blank" rel="noopener" @endif>{{ $item->label }}</a></li>
                @endforeach
                @if ($navCategories->isNotEmpty())
                    <li class="nav__dd" data-dropdown>
                        <button type="button" class="nav__link nav__dd-btn" aria-expanded="false" aria-haspopup="true" data-dropdown-btn>
                            Category <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                        </button>
                        <ul class="nav__dd-menu">
                            <li><a href="{{ route('portfolio.projects') }}">Semua Project</a></li>
                            @foreach ($navCategories as $cat)
                                <li><a href="{{ route('portfolio.projects', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            </ul>

            <div class="nav__end">
                <div class="theme-switch" role="radiogroup" aria-label="Tema tampilan" data-theme-switch>
                    <button type="button" role="radio" aria-checked="false" data-theme-set="light" aria-label="Tema terang" title="Terang"><i class="fa-solid fa-sun" aria-hidden="true"></i></button>
                    <button type="button" role="radio" aria-checked="false" data-theme-set="system" aria-label="Ikuti sistem" title="Ikuti sistem"><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i></button>
                    <button type="button" role="radio" aria-checked="false" data-theme-set="dark" aria-label="Tema gelap" title="Gelap"><i class="fa-solid fa-moon" aria-hidden="true"></i></button>
                </div>
                <a href="{{ $link($c['nav_cta_link']) }}" class="nav__cta"><span data-pf="nav_cta_label">{{ $c['nav_cta_label'] }}</span></a>
                <button class="nav__toggle" type="button" aria-controls="nav-menu" aria-expanded="false" data-nav-toggle aria-label="Buka menu">
                    <span class="nav__burger" aria-hidden="true"><i></i><i></i><i></i></span>
                </button>
            </div>
        </div>

        {{-- Full-screen menu (mobile, or the burger at any width) --}}
        <nav class="nav__menu" id="nav-menu" aria-label="Navigasi utama">
            <div class="container nav__menu-inner">
                <ul class="nav__menu-links">
                    @foreach ($nav as $i => $item)
                        <li><a href="{{ $link($item->target) }}" @if (str_starts_with($item->target, 'http')) target="_blank" rel="noopener" @endif>
                            <small>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</small>{{ $item->label }}</a></li>
                    @endforeach
                    @if ($navCategories->isNotEmpty())
                        <li><a href="{{ route('portfolio.projects') }}"><small>{{ str_pad($nav->count() + 1, 2, '0', STR_PAD_LEFT) }}</small>Semua Project</a></li>
                    @endif
                </ul>
                <div class="nav__menu-side">
                    <form action="{{ route('portfolio.projects') }}" method="GET" class="nav__search" role="search">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <label for="nav-q2" class="sr-only">Cari project</label>
                        <input id="nav-q2" type="search" name="q" placeholder="Cari project" maxlength="80" value="{{ $search ?? '' }}" autocomplete="off">
                    </form>
                    <a href="{{ $link($c['nav_cta_link']) }}" class="btn btn--glow">{{ $c['nav_cta_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @include('frontend.partials.socials', ['class' => 'socials'])
                </div>
            </div>
        </nav>
    </header>

    {{-- Preloader: logo in a spinning ring, brand name, progress bar (portfolio.js hides it on load) --}}
    <div class="loader" data-loader aria-hidden="true">
        <span class="loader__ring"><i></i><img src="{{ $brandLogo ? Media::url($brandLogo) : $brand['logo_url'] }}" alt="" width="96" height="96"></span>
        <span class="loader__name">{{ $c['brand_name'] }}</span>
        <span class="loader__text">Loading<b>.</b><b>.</b><b>.</b></span>
        <span class="loader__bar"><i></i></span>
    </div>

    <main id="main">
        @yield('content')
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="footer" id="footer">
        <div class="footer__ghost" aria-hidden="true"><span>{{ Str::upper(Str::before(trim((string) ($c['about_name'] ?: $c['brand_name'])), ' ') ?: $c['brand_name']) }}</span></div>
        <div class="container footer__inner">
            <div class="footer__connect" data-reveal>
                <a href="{{ route('home') }}" class="footer__brand" aria-label="{{ $c['brand_name'] }}">
                    @if ($brandLogo)
                        <img src="{{ Media::url($brandLogo) }}" alt="{{ $c['brand_name'] }}" height="40" width="132" loading="lazy">
                    @else
                        <img src="{{ $brand['logo_url'] }}" alt="" width="44" height="44" loading="lazy">
                        <span data-pf="brand_name">{{ $c['brand_name'] }}</span>
                    @endif
                </a>
                <p class="footer__kicker" data-pf="footer_about">{{ $c['footer_about'] }}</p>
                <h2 class="footer__cta-title" data-pf="footer_cta_title">{{ $c['footer_cta_title'] }}</h2>
                <a href="{{ request()->routeIs('home') ? '#contact' : route('home') . '#contact' }}" class="btn btn--glow btn--lg">
                    {{ $c['hero_cta_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="footer__grid">
                @foreach (\App\Models\Portfolio\FooterLink::COLUMNS as $colKey => $colDefault)
                    @php $items = $footerLinks[$colKey] ?? collect(); @endphp
                    @if ($items->isNotEmpty())
                        <div>
                            <h3 class="footer__heading" data-pf="footer_{{ $colKey === 'nav' ? 'nav' : 'contact' }}_title">{{ $c["footer_{$colKey}_title"] ?? $colDefault }}</h3>
                            <ul class="footer__links">
                                @foreach ($items as $fl)
                                    @php $external = $fl->url && (str_starts_with($fl->url, 'http') && ! str_starts_with($fl->url, url('/'))); @endphp
                                    <li>
                                        @if ($fl->url)
                                            <a href="{{ $link($fl->url) }}" @if ($external) target="_blank" rel="noopener" @endif>
                                                @if ($fl->icon_class)<i class="{{ $fl->icon_class }}" aria-hidden="true"></i>@endif
                                                <span data-pf="footer.{{ $fl->id }}.label">{{ $fl->label }}</span>
                                            </a>
                                        @else
                                            <span>
                                                @if ($fl->icon_class)<i class="{{ $fl->icon_class }}" aria-hidden="true"></i>@endif
                                                <span data-pf="footer.{{ $fl->id }}.label">{{ $fl->label }}</span>
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
                @if ($nav->isNotEmpty())
                    <div>
                        <h3 class="footer__heading">Eksplorasi</h3>
                        <ul class="footer__links">
                            @foreach ($nav as $item)
                                <li><a href="{{ $link($item->target) }}">{{ $item->label }}</a></li>
                            @endforeach
                            <li><a href="{{ route('portfolio.projects') }}">Semua Project</a></li>
                        </ul>
                    </div>
                @endif
            </div>

            <div class="footer__bottom">
                <p>© {{ now()->year }} <span data-pf="footer_copyright">{{ trim((string) $c['footer_copyright']) ?: ($c['about_name'] ?: $c['brand_name']) }}</span>. All rights reserved.</p>
                @if (($c['footer_show_socials'] ?? '1') === '1')
                    @include('frontend.partials.socials', ['class' => 'socials'])
                @endif
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/front/js/portfolio.js') }}?v={{ filemtime(public_path('assets/front/js/portfolio.js')) }}" defer></script>
    @if (request()->boolean('pf_preview'))
        <script src="{{ asset('assets/front/js/preview.js') }}?v={{ filemtime(public_path('assets/front/js/preview.js')) }}" defer></script>
    @endif
    @stack('scripts')
</body>
</html>
