<?php

namespace App\Models\Concerns;

use App\Support\Portfolio\PortfolioCache;
use Illuminate\Database\Eloquent\Builder;

/**
 * Shared behaviour for every table the public portfolio reads:
 * an "active, in display order" scope and cache invalidation on write.
 */
trait PortfolioContent
{
    public static function bootPortfolioContent(): void
    {
        static::saved(fn () => PortfolioCache::flush());
        static::deleted(fn () => PortfolioCache::flush());
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
