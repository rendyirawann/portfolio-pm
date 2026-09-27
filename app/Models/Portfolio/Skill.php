<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use PortfolioContent;

    protected $table = 'skills';

    protected $fillable = ['name', 'category', 'level', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
