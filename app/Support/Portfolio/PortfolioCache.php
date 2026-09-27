<?php

namespace App\Support\Portfolio;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Versioned cache for the public pages. Any write to portfolio content bumps
 * the version, which retires every cached page at once (home, project
 * details, sitemap) without having to know their keys.
 */
class PortfolioCache
{
    private const VERSION_KEY = 'portfolio.cache.version';

    private const TTL = 86400;

    public static function remember(string $key, Closure $callback): mixed
    {
        return Cache::remember(self::version() . ':' . $key, self::TTL, $callback);
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, (string) microtime(true));
    }

    private static function version(): string
    {
        return 'portfolio.' . Cache::rememberForever(self::VERSION_KEY, fn () => (string) microtime(true));
    }
}
