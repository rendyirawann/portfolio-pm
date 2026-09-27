<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use App\Support\Portfolio\SocialPlatform;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use PortfolioContent;

    protected $fillable = ['platform', 'label', 'url', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        // The platform — and so the icon — always follows the pasted URL.
        static::saving(function (self $link) {
            $link->platform = SocialPlatform::detect($link->url);
        });
    }

    public function getIconAttribute(): string
    {
        return SocialPlatform::icon($this->platform);
    }

    public function getDisplayLabelAttribute(): string
    {
        return $this->label ?: SocialPlatform::name($this->platform);
    }
}
