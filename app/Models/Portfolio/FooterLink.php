<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use App\Support\Portfolio\SocialPlatform;
use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    use PortfolioContent;

    public const COLUMNS = ['nav' => 'Navigasi', 'contact' => 'Kontak'];

    protected $fillable = ['column', 'label', 'url', 'icon', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Custom icon, else one detected from the link (none for plain nav links). */
    public function getIconClassAttribute(): ?string
    {
        if ($this->icon) {
            return $this->icon;
        }

        if ($this->column === 'nav') {
            return null;
        }

        return $this->url ? SocialPlatform::icon(SocialPlatform::detect($this->url)) : 'fa-solid fa-location-dot';
    }
}
