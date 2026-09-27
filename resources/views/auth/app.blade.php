<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.theme-boot')

    <script>
        // Marks when the entry preloader became visible, so app-auth.js can
        // hold it for exactly 1.2s no matter how fast the page loaded.
        window.__authPreloadStart = Date.now();

        // Click-jacking backstop for browsers that ignore X-Frame-Options.
        // The admin's same-origin live preview is the one allowed frame.
        if (window.top !== window.self && !@json($pfPreview ?? false)) {
            window.top.location.replace(window.self.location.href);
        }
    </script>

    @include('partials.meta')

    {{-- The topology background pulls p5 + Vanta from here. --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $brand['font']) }}:wght@300;400;500;600;700;800&display=swap" />

    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app-auth.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@600;700&family=Inter:wght@400;500;600&family=Chakra+Petch:wght@500;600&family=Playfair+Display:wght@600&display=swap" />
    <link href="{{ asset('assets/css/app-auth-pf.css') }}?v={{ filemtime(public_path('assets/css/app-auth-pf.css')) }}" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --bs-font-sans-serif: '{{ $brand['font'] }}', system-ui, sans-serif;
            /* Still artwork behind the animated canvas — also the fallback
               when the Vanta CDN is unavailable. */
            --auth-bg-light: url('{{ asset('assets/media/auth/bg11.jpg') }}');
            --auth-bg-dark: url('{{ asset('assets/media/auth/bg11-dark.jpg') }}');
        }

        body,
        .auth-card,
        .auth-story {
            font-family: '{{ $brand['font'] }}', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }
    </style>

    @stack('stylesheets')
</head>

<body class="auth-body">

    {{-- Light / dark — shares the admin's theme key. --}}
    <button type="button" class="auth-theme-toggle" id="auth-theme-toggle" aria-label="Ganti tema terang / gelap" title="Tema terang / gelap">
        <i class="ki-outline ki-moon fs-3" data-when="dark"></i><i class="ki-outline ki-sun fs-3" data-when="light"></i>
    </button>

    {{-- Entry preloader: rendered server-side so it is on screen from the
         very first paint. app-auth.js fades it out after 1.2s; the CSS
         carries the same timing as a no-JS failsafe. --}}
    <div id="auth-preloader" role="status" aria-live="polite">
        <div class="auth-preloader__mark">
            <span class="auth-preloader__ring" aria-hidden="true"></span>
            <span class="auth-preloader__ring auth-preloader__ring--alt" aria-hidden="true"></span>
            <img src="{{ $login['logo'] }}" alt="" width="56" height="56" />
        </div>
        <div class="auth-preloader__label" data-pf="login_loader_label">{{ $login['loader_label'] }}</div>
        <div class="auth-preloader__bar" aria-hidden="true"><span></span></div>
        <div class="auth-preloader__text" data-pf="login_loader_text">{{ $login['loader_text'] }}</div>
    </div>

    <div class="auth-shell">
        <div class="auth-bg" aria-hidden="true"></div>

        {{-- Animated topology background (Vanta + p5), loaded lazily. --}}
        {{-- Ground colours match --auth-splash so the preloader, the canvas
             and the dashboard reveal all share one background. The line
             colour is a step stronger than the accent, because topology
             draws very thin strokes. --}}
        <div id="auth-vanta" aria-hidden="true"
            data-color-light="#e11d48" data-bg-light="#f5f6fa"
            data-color-dark="#ff2d55" data-bg-dark="#06060c"></div>

        <!--begin::Story panel-->
        <section class="auth-story">
            <a href="{{ url('/') }}" class="auth-story__brand text-decoration-none">
                <img src="{{ $login['logo'] }}" alt="" class="auth-story__logo" width="52" height="52" />
                <span>
                    <span class="auth-story__name d-block" data-pf="login_brand_name">{{ $login['name'] }}</span>
                    <span class="auth-story__tagline" data-pf="login_tagline">{{ $login['tagline'] }}</span>
                </span>
            </a>

            <div>
                <p class="auth-story__kicker" data-pf="login_kicker">{{ $login['kicker'] }}</p>
                <h1 class="auth-story__headline"><span class="auth-story__h1" data-pf="login_headline_1">{{ $login['headline_1'] }}</span><span data-pf="login_headline_2">{{ $login['headline_2'] }}</span></h1>
                <p class="auth-story__lead" data-pf="login_lead">{{ $login['lead'] }}</p>

                <ul class="auth-story__points">
                    @foreach ($login['points'] as $i => $point)
                        <li>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
                            <span data-pf="login_point_{{ $i + 1 }}">{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="auth-story__foot mb-0">
                &copy; {{ now()->year }} <span data-pf="login_brand_name">{{ $login['name'] }}</span> &middot; <span data-pf="login_tagline">{{ $login['tagline'] }}</span>
            </p>
        </section>
        <!--end::Story panel-->

        <!--begin::Form panel-->
        <main class="auth-panel">
            @yield('content')
        </main>
        <!--end::Form panel-->
    </div>

    <!--begin::Sign-in progress overlay-->
    <div id="auth-progress" role="status" aria-live="polite" aria-hidden="true" data-steps='@json(array_slice($login['progress'], 1))'>
        <div class="auth-progress__ring" aria-hidden="true"></div>
        <p class="auth-progress__step" data-progress-step data-pf="login_progress_1">{{ $login['progress'][0] ?? 'Memverifikasi kredensial' }}</p>
        <div class="auth-progress__dots" data-progress-dots aria-hidden="true">
            <i></i><i></i><i></i>
        </div>
    </div>
    <!--end::Sign-in progress overlay-->

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/app-auth.js') }}"></script>
    <script>
        document.getElementById('auth-theme-toggle').addEventListener('click', function () {
            var html = document.documentElement;
            var next = html.getAttribute('data-bs-theme') === 'light' ? 'dark' : 'light';
            html.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('data-bs-theme', next); } catch (e) {}
        });
    </script>
    @stack('scripts')
    @if ($pfPreview ?? false)
        <script>window.__pfLoginSteps = @json($login['progress']);</script>
        <script src="{{ asset('assets/front/js/preview.js') }}?v={{ filemtime(public_path('assets/front/js/preview.js')) }}" defer></script>
    @endif

    @include('partials._rt')
</body>

</html>
