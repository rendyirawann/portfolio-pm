<?php

namespace App\Models\Portfolio;

use App\Support\Portfolio\PortfolioCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectLink extends Model
{
    protected $fillable = ['project_id', 'label', 'url', 'sort_order'];

    protected static function booted(): void
    {
        static::saved(fn () => PortfolioCache::flush());
        static::deleted(fn () => PortfolioCache::flush());
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
