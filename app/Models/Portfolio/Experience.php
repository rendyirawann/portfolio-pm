<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use PortfolioContent;

    protected $table = 'experiences';

    protected $fillable = ['role', 'company', 'period', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
