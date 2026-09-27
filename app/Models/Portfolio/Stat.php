<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use PortfolioContent;

    protected $table = 'stats';

    protected $fillable = ['value', 'label', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
