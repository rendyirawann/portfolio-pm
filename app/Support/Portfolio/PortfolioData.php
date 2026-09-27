<?php

namespace App\Support\Portfolio;

use App\Models\Portfolio\Experience;
use App\Models\Portfolio\NavItem;
use App\Models\Portfolio\PageContent;
use App\Models\Portfolio\Project;
use App\Models\Portfolio\ProjectCategory;
use App\Models\Portfolio\Service;
use App\Models\Portfolio\Skill;
use App\Models\Portfolio\SocialLink;
use App\Models\Portfolio\Stat;
use App\Models\Portfolio\Testimonial;

/**
 * Read side of the public portfolio. Each loader runs a handful of narrow,
 * indexed queries once and is then served from PortfolioCache until the
 * admin changes something.
 */
class PortfolioData
{
    /** Content map + shared chrome (nav, socials) used by every public page. */
    public static function layout(): array
    {
        return PortfolioCache::remember('layout', fn () => [
            'content' => PageContent::query()->pluck('value', 'key')->all() + ContentSchema::defaults(),
            'nav' => NavItem::visible()->get(['id', 'label', 'target']),
            'socials' => SocialLink::visible()->get(['id', 'platform', 'label', 'url']),
            'navCategories' => self::categories(),
        ]);
    }

    public static function home(): array
    {
        return PortfolioCache::remember('home', fn () => [
            'stats' => Stat::visible()->get(['id', 'value', 'label']),
            'services' => Service::visible()->get(['id', 'title', 'icon', 'description']),
            'skills' => Skill::visible()->get(['id', 'name', 'category', 'level'])
                ->groupBy(fn ($s) => $s->category ?: 'General'),
            'experiences' => Experience::visible()->get(['id', 'role', 'company', 'period', 'description']),
            'testimonials' => Testimonial::visible()->get(['id', 'name', 'position', 'quote', 'avatar']),
            'projects' => self::projectCards(9),
            'categories' => self::categories(),
            'projectTotal' => Project::where('is_published', true)->count(),
        ]);
    }

    public static function projectCards(?int $limit = null)
    {
        return Project::visible()
            ->select(['id', 'project_category_id', 'type', 'title', 'slug', 'summary', 'cover_image', 'year', 'tech_stack', 'is_featured'])
            ->with(['category:id,name,slug', 'images' => fn ($q) => $q->select('id', 'project_id', 'path', 'thumb_path')->limit(1)])
            ->when($limit, fn ($q) => $q->limit($limit))
            ->get();
    }

    public static function categories()
    {
        return ProjectCategory::visible()
            ->whereHas('projects', fn ($q) => $q->where('is_published', true))
            ->get(['id', 'name', 'slug']);
    }

    public static function project(string $slug): ?Project
    {
        return PortfolioCache::remember('project:' . md5($slug), function () use ($slug) {
            $project = Project::where('slug', $slug)->where('is_published', true)
                ->with(['category:id,name,slug', 'images', 'files', 'links'])
                ->first();

            if (! $project) {
                return null;
            }

            $project->setRelation('related', Project::visible()
                ->select(['id', 'project_category_id', 'type', 'title', 'slug', 'summary', 'cover_image', 'year', 'tech_stack', 'is_featured'])
                ->with(['category:id,name,slug', 'images' => fn ($q) => $q->select('id', 'project_id', 'path', 'thumb_path')->limit(1)])
                ->whereKeyNot($project->id)
                ->when($project->project_category_id, fn ($q) => $q->where('project_category_id', $project->project_category_id))
                ->limit(3)->get());

            return $project;
        });
    }
}
