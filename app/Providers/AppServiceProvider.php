<?php

namespace App\Providers;

use App\Support\Brand;
use App\View\Composers\NotificationComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Resolved once per request (and reset between Octane requests).
        $this->app->scoped(Brand::KEY, fn () => Brand::compute());
    }

    public function boot(): void
    {
        $this->configureUrls();
        $this->configureAuthorization();
        $this->configurePasswordPolicy();
        $this->configureRateLimiting();
        $this->shareBranding();
    }

    /** Force canonical absolute URLs behind TLS in production. */
    private function configureUrls(): void
    {
        // APP_URL is the single source of truth for scheme, host and base
        // path — "http://103.1.2.3/portfolio" and "https://domain.com" both
        // work without code changes, so moving to a domain is an .env edit.
        if (config('app.env') === 'production' && filled(config('app.url'))) {
            $root = rtrim((string) config('app.url'), '/');
            URL::forceRootUrl($root);
            URL::forceScheme(str_starts_with($root, 'https://') ? 'https' : 'http');
        }
    }

    private function configureAuthorization(): void
    {
        // Superadmin implicitly holds every permission.
        Gate::before(function ($user, $ability) {
            return $user->hasRole(['Superadmin', 'superadmin']) ? true : null;
        });
    }

    /** One password policy for registration, reset and change-password. */
    private function configurePasswordPolicy(): void
    {
        Password::defaults(function () {
            $rule = Password::min(8)->letters()->numbers();

            return $this->app->isProduction()
                ? $rule->mixedCase()->uncompromised()
                : $rule;
        });
    }

    /**
     * Named limiters used across web.php, auth.php and api.php.
     *
     * Authenticated users are keyed by id so several people behind one office
     * NAT never throttle each other.
     */
    private function configureRateLimiting(): void
    {
        $limits = config('security.rate_limits');

        RateLimiter::for('web', fn (Request $request) => Limit::perMinute($limits['web'])
            ->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute($limits['api'])
            ->by($request->user()?->id ?: $request->ip()));

        // Writes are cheap to abuse and expensive to serve — keep them tighter.
        RateLimiter::for('write', fn (Request $request) => Limit::perMinute($limits['write'])
            ->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute($limits['login'])
                ->by(strtolower((string) $request->input('email')) . '|' . $request->ip()),
            Limit::perMinute($limits['login'] * 4)->by($request->ip()),
        ]);

        // Public contact form: a burst limit plus a daily cap per IP.
        RateLimiter::for('contact', fn (Request $request) => [
            Limit::perMinute(3)->by('contact-m|' . $request->ip())
                ->response(fn () => $this->contactThrottled($request)),
            Limit::perDay(15)->by('contact-d|' . $request->ip())
                ->response(fn () => $this->contactThrottled($request)),
        ]);

        RateLimiter::for('password', fn (Request $request) => Limit::perMinute($limits['password'])
            ->by(strtolower((string) $request->input('email')) . '|' . $request->ip()));
    }

    private function contactThrottled(Request $request)
    {
        $text = 'Terlalu banyak pesan terkirim. Silakan coba lagi nanti atau hubungi via WhatsApp.';

        return $request->expectsJson()
            ? response()->json(['message' => $text], 429)
            : redirect()->to(url('/') . '#contact')->withErrors(['message' => $text])->withInput();
    }

    /** Expose settings + resolved brand identity to every view. */
    private function shareBranding(): void
    {
        View::composer('*', function ($view) {
            $view->with('brand', Brand::all());

            // Kept for backwards compatibility with existing templates.
            $view->with('appSettings', $this->legacySettings());
        });

        View::composer('backend.layout.navbar', NotificationComposer::class);

        // Admin header / sidebar / footer branding (Konten Halaman › Navbar & Footer Admin).
        View::composer(
            ['backend.layout.app', 'backend.layout.sidebar', 'backend.layout.footer'],
            fn ($view) => $view->with('chrome', \App\Support\Portfolio\PortfolioData::admin())
        );
    }

    /** @return array<string,string> */
    private function legacySettings(): array
    {
        try {
            return \App\Models\Setting::allCached();
        } catch (\Throwable) {
            return [];
        }
    }
}
