<?php

namespace App\Models\Portfolio;

use App\Support\Portfolio\PortfolioCache;
use Illuminate\Database\Eloquent\Model;

/** Key/value copy for the single-instance sections of the portfolio. */
class PageContent extends Model
{
    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => PortfolioCache::flush());
        static::deleted(fn () => PortfolioCache::flush());
    }
}
