<?php

namespace App\Models\Portfolio;

use App\Support\Portfolio\PortfolioCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFile extends Model
{
    protected $fillable = ['project_id', 'path', 'original_name', 'mime', 'size', 'sort_order'];

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
