<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use PortfolioContent;

    protected $table = 'project_categories';

    protected $fillable = ['name', 'slug', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Project::class);
    }
}
