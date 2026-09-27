@php
    use App\Support\Portfolio\Media;
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&family=Chakra+Petch:wght@500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('assets/front/css/portfolio.css') }}?v={{ filemtime(public_path('assets/front/css/portfolio.css')) }}">
    @stack('head')
    <script nonce="{{ $nonce }}">document.documentElement.classList.replace('no-js', 'js');</script>
</head>
<body class="@yield('body_class')">
    <a class="skip-link" href="#main">Lewati ke konten</a>

    {{-- ================= NAVBAR ================= --}}
    <header class="nav" data-nav>
        <div class="container nav__bar">
            <a href="{{ route('home') }}" class="nav__brand" aria-label="{{ $c['brand_name'] }} — beranda">
                @if ($brandLogo)
                    <img src="{{ Media::url($brandLogo) }}" alt="{{ $c['brand_name'] }}" height="36" width="120" class="nav__logo">
                @else
                    <img src="{{ $brand['logo_url'] }}" alt="" class="nav__mark" width="40" height="40">
                    <span class="nav__name" data-pf="brand_name">{{ $c['brand_name'] }}</span>
                @endif
            </a>

            @php
                $link = fn ($t) => \App\Support\Portfolio\PortfolioData::href($t);
            @endphp

            {{-- Right cluster: Home · Category ▾ · Search · Menu ≡ --}}
            <div class="nav__right">
                <a href="{{ route('home') }}" class="nav__link">Home</a>

                @if ($navCategories->isNotEmpty())
                    <div class="nav__dd" data-dropdown>
                        <button type="button" class="nav__link nav__dd-btn" aria-expanded="false" aria-haspopup="true" data-dropdown-btn>
                            Category <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                        </button>
                        <ul class="nav__dd-menu">
                            <li><a href="{{ route('portfolio.projects') }}">Semua Project</a></li>
                            @foreach ($navCategories as $cat)
                                <li><a href="{{ route('portfolio.projects', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('portfolio.projects') }}" method="GET" class="nav__search" role="search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <label for="nav-q" class="sr-only">Cari project</label>
                    <input id="nav-q" type="search" name="q" placeholder="Search" maxlength="80" value="{{ $search ?? '' }}" autocomplete="off">
                </form>

                <button class="nav__toggle" type="button" aria-controls="nav-menu" aria-expanded="false" data-nav-toggle>
                    <span class="nav__toggle-label">Menu</span>
                    <span class="nav__burger" aria-hidden="true"><i></i><i></i><i></i></span>
                </button>
            </div>
        </div>

        {{-- Full-screen menu opened by "Menu" --}}
        <nav class="nav__menu" id="nav-menu" aria-label="Navigasi utama">
            <div class="container nav__menu-inner">
                <ul class="nav__menu-links">
                    @foreach ($nav as $i => $item)
                        <li><a href="{{ $link($item->target) }}" @if (str_starts_with($item->target, 'http')) target="_blank" rel="noopener" @endif>
                            <small>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</small>{{ $item->label }}</a></li>
                    @endforeach
                </ul>
                <div class="nav__menu-side">
                    <form action="{{ route('portfolio.projects') }}" method="GET" class="nav__search nav__search--menu" role="search">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <label for="nav-q2" class="sr-only">Cari project</label>
                        <input id="nav-q2" type="search" name="q" placeholder="Search project" maxlength="80">
                    </form>
                    <a href="{{ $link($c['nav_cta_link']) }}" class="btn btn--glow">{{ $c['nav_cta_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @include('frontend.partials.socials', ['class' => 'socials'])
                </div>
            </div>
        </nav>
    </header>

    <main id="main">
        @yield('content')
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="footer" id="footer">
        <div class="container">
            <div class="footer__cta" data-reveal>
                <h2 class="footer__cta-title" data-pf="footer_cta_title">{{ $c['footer_cta_title'] }}</h2>
                <a href="{{ request()->routeIs('home') ? '#contact' : route('home') . '#contact' }}" class="btn btn--glow">
                    {{ $c['hero_cta_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="footer__grid">
                <div class="footer__about">
                    <a href="{{ route('home') }}" class="nav__brand">
                        @if ($brandLogo)
                            <img src="{{ Media::url($brandLogo) }}" alt="{{ $c['brand_name'] }}" height="36" width="120" class="nav__logo" loading="lazy">
                        @else
                            <img src="{{ $brand['logo_url'] }}" alt="" class="nav__mark" width="40" height="40" loading="lazy">
                            <span class="nav__name" data-pf="brand_name">{{ $c['brand_name'] }}</span>
                        @endif
                    </a>
                    <p data-pf="footer_about">{{ $c['footer_about'] }}</p>
                </div>

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
            </div>

            <div class="footer__bottom">
                <p data-pf="footer_copyright">{{ \App\Support\Portfolio\PortfolioData::fill($c['footer_copyright'], $c['about_name'] ?: $c['brand_name']) }}</p>
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
