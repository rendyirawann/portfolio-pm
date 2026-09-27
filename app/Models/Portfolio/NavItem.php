<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use Illuminate\Database\Eloquent\Model;

class NavItem extends Model
{
    use PortfolioContent;

    protected $table = 'nav_items';

    protected $fillable = ['label', 'target', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
