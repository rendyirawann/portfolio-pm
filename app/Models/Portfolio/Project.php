<?php

namespace App\Models\Portfolio;

use App\Models\Concerns\PortfolioContent;
use App\Support\Portfolio\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Parent record for a portfolio entry. Its gallery, downloadable files and
 * external links live in the project_images / project_files / project_links
 * child tables.
 */
class Project extends Model
{
    use PortfolioContent;

    public const TYPES = ['project' => 'Project', 'product' => 'Product'];

    protected $fillable = [
        'project_category_id', 'type', 'title', 'slug', 'summary', 'description', 'cover_image',
        'client', 'year', 'tech_stack', 'meta_title', 'meta_description',
        'is_featured', 'is_published', 'sort_order',
    ];

    protected $casts = ['is_featured' => 'boolean', 'is_published' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (self $project) {
            if (blank($project->slug)) {
                $project->slug = static::uniqueSlug($project->title, $project->id);
            }
        });

        // Children cascade in the DB; their files on disk must go too.
        static::deleting(function (self $project) {
            Media::delete($project->cover_image);
            $project->images->each(fn ($i) => Media::delete($i->path, $i->thumb_path));
            $project->files->each(fn ($f) => Media::delete($f->path));
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'project';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class)->orderBy('sort_order')->orderBy('id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(ProjectLink::class)->orderBy('sort_order')->orderBy('id');
    }

    /** Cover image, falling back to the first gallery image. */
    public function getCoverUrlAttribute(): string
    {
        $path = $this->cover_image ?: $this->images->first()?->thumb_path ?: $this->images->first()?->path;

        return Media::url($path, 'assets/front/img/project-placeholder.svg');
    }

    /** @return list<string> */
    public function getTechListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->tech_stack))));
    }
}
