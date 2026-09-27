<?php

namespace App\Models\Portfolio;

use App\Support\Portfolio\PortfolioCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    protected $fillable = ['project_id', 'path', 'thumb_path', 'caption', 'width', 'height', 'sort_order'];

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
