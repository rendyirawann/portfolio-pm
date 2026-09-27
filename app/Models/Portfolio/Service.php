<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use PortfolioContent;

    protected $table = 'services';

    protected $fillable = ['title', 'icon', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
