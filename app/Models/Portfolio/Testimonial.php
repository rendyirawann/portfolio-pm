<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use PortfolioContent;

    protected $table = 'testimonials';

    protected $fillable = ['name', 'position', 'quote', 'avatar', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
