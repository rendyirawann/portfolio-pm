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
            'footerLinks' => \App\Models\Portfolio\FooterLink::visible()->get(['id', 'column', 'label', 'url', 'icon'])->groupBy('column'),
        ]);
    }

    /**
     * Turn a link typed in the admin into a working href, wherever the site
     * is mounted: "#about" (home section), "/projects" (relative to the app,
     * so it keeps a /subfolder base), or a full URL / mailto: / tel: as-is.
     */
    public static function href(?string $target): string
    {
        $target = trim((string) $target);

        if ($target === '') {
            return url('/');
        }
        if (str_starts_with($target, '#')) {
            return request()->routeIs('home') ? $target : route('home') . $target;
        }
        if (str_starts_with($target, '/') && ! str_starts_with($target, '//')) {
            return url($target);
        }

        return $target;
    }

    /** "© 2026 Name" — the year is always the current one; only the name is editable. */
    public static function copyright(?string $name, string $fallback): string
    {
        return '© ' . now()->year . ' ' . (trim((string) $name) ?: $fallback);
    }

    /**
     * Branding for the admin header, sidebar and footer — edited under
     * Konten Halaman › Navbar & Footer Admin, falling back to Settings.
     */
    public static function admin(): array
    {
        try {
            $layout = self::layout();
        } catch (\Throwable) {
            $layout = ['content' => ContentSchema::defaults(), 'socials' => collect()];
        }

        $c = $layout['content'];
        $brand = \App\Support\Brand::all();
        $name = trim((string) ($c['admin_brand_name'] ?? '')) ?: $brand['name'];

        return [
            'name' => $name,
            'tagline' => trim((string) ($c['admin_brand_tagline'] ?? '')) ?: $brand['tagline'],
            'logo' => ! empty($c['admin_logo']) ? Media::url($c['admin_logo']) : $brand['logo_url'],
            'footer_name' => trim((string) ($c['admin_footer_text'] ?? '')) ?: $name,
            'footer' => self::copyright($c['admin_footer_text'] ?? '', $name) . ' · ' . (trim((string) ($c['admin_brand_tagline'] ?? '')) ?: $brand['tagline']),
            'footer_link' => ($c['admin_footer_link'] ?? '') ?: null,
            'socials' => ($c['admin_footer_show_socials'] ?? '1') === '1' ? $layout['socials'] : collect(),
        ];
    }

    /** Copy for the sign-in screen and its loaders (Konten Halaman › Halaman Login & Loader). */
    public static function login(): array
    {
        try {
            $c = self::layout()['content'];
        } catch (\Throwable) {
            $c = ContentSchema::defaults();
        }

        $brand = \App\Support\Brand::all();
        $get = fn (string $k) => trim((string) ($c[$k] ?? '')) ?: (string) (ContentSchema::defaults()[$k] ?? '');
        $name = $get('login_brand_name') ?: $brand['name'];

        return [
            'name' => $name,
            'tagline' => $get('login_tagline') ?: $brand['tagline'],
            'kicker' => $get('login_kicker'),
            'headline_1' => $get('login_headline_1'),
            'headline_2' => $get('login_headline_2'),
            'lead' => $get('login_lead'),
            'points' => array_values(array_filter([$get('login_point_1'), $get('login_point_2'), $get('login_point_3')])),
            'card_title' => $get('login_card_title'),
            'card_subtitle' => $get('login_card_subtitle'),
            'button' => $get('login_button'),
            'button_busy' => $get('login_button_busy'),
            'foot' => $get('login_foot'),
            'loader_label' => trim((string) ($c['login_loader_label'] ?? '')) ?: $name,
            'loader_text' => $get('login_loader_text'),
            'progress' => array_values(array_filter([$get('login_progress_1'), $get('login_progress_2'), $get('login_progress_3'), $get('login_progress_4')])),
            'logo' => $brand['logo_url'],
        ];
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
