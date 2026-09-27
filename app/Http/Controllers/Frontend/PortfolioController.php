<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Portfolio\Project;
use App\Support\Portfolio\PortfolioCache;
use App\Support\Portfolio\PortfolioData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function home(): View
    {
        return view('frontend.home', PortfolioData::layout() + PortfolioData::home());
    }

    public function projects(Request $request): View
    {
        $q = Str::limit(trim((string) $request->query('q', '')), 80, '');
        $category = (string) $request->query('category', '');

        // The full list is cached; searching filters it in memory, so user
        // input never reaches SQL.
        $projects = PortfolioCache::remember('projects.all', fn () => PortfolioData::projectCards());
        if ($q !== '') {
            $needle = Str::lower($q);
            $projects = $projects->filter(fn ($p) => Str::contains(
                Str::lower($p->title . ' ' . $p->summary . ' ' . $p->tech_stack . ' ' . $p->category?->name), $needle
            ))->values();
        }

        $categories = PortfolioCache::remember('categories', fn () => PortfolioData::categories());

        return view('frontend.projects', PortfolioData::layout() + [
            'projects' => $projects,
            'categories' => $categories,
            'activeCategory' => $categories->contains('slug', $category) ? $category : null,
            'search' => $q,
        ]);
    }

    public function project(string $slug): View
    {
        abort_unless(preg_match('/^[a-z0-9\-_]{1,170}$/i', $slug) === 1, 404);

        $project = PortfolioData::project($slug);
        abort_if($project === null, 404);

        return view('frontend.project', PortfolioData::layout() + ['project' => $project]);
    }

    public function sitemap(): Response
    {
        $xml = PortfolioCache::remember('sitemap', function () {
            $projects = Project::where('is_published', true)->get(['slug', 'updated_at']);

            return view('frontend.sitemap', ['projects' => $projects])->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $robots = PortfolioData::layout()['content']['seo_robots'] ?? 'index, follow';
        $lines = ['User-agent: *'];

        if (str_starts_with($robots, 'noindex')) {
            $lines[] = 'Disallow: /';
        } else {
            $lines[] = 'Allow: /';
            $lines[] = 'Disallow: /admin';
            $lines[] = '';
            $lines[] = 'Sitemap: ' . route('sitemap');
        }

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
