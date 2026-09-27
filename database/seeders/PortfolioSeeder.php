<?php

namespace Database\Seeders;

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
use App\Support\Portfolio\ContentSchema;
use App\Support\Portfolio\PortfolioCache;
use App\Support\Portfolio\SocialPlatform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Starter content for the public portfolio. Safe to re-run: page copy is
 * only filled where missing and the list tables are only seeded when empty.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ContentSchema::defaults() as $key => $value) {
            PageContent::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->seed(NavItem::class, [
            ['label' => 'Home', 'target' => '#home'],
            ['label' => 'About', 'target' => '#about'],
            ['label' => 'Services', 'target' => '#services'],
            ['label' => 'Projects', 'target' => '#projects'],
            ['label' => 'Experience', 'target' => '#experience'],
            ['label' => 'Contact', 'target' => '#contact'],
        ]);

        $this->seed(Stat::class, [
            ['value' => '07', 'label' => 'Years Experience'],
            ['value' => '60+', 'label' => 'Projects Shipped'],
            ['value' => '35+', 'label' => 'Happy Clients'],
            ['value' => '99%', 'label' => 'On-time Delivery'],
        ]);

        $this->seed(Service::class, [
            ['title' => 'Web Development', 'icon' => 'fa-solid fa-code', 'description' => 'Fast, secure and SEO-ready websites and web apps built with Laravel, React and modern tooling.'],
            ['title' => 'AI Integration', 'icon' => 'fa-solid fa-brain', 'description' => 'Chatbots, automation and smart features powered by LLMs, wired safely into your product.'],
            ['title' => 'UI / UX Design', 'icon' => 'fa-solid fa-pen-ruler', 'description' => 'Interfaces that look sharp and feel effortless — from wireframe to polished design system.'],
            ['title' => 'Mobile Apps', 'icon' => 'fa-solid fa-mobile-screen', 'description' => 'Cross-platform mobile apps with a native feel, backed by a solid API.'],
            ['title' => 'API & Backend', 'icon' => 'fa-solid fa-server', 'description' => 'Scalable REST APIs, integrations and dashboards with clean, maintainable architecture.'],
            ['title' => 'Maintenance & Security', 'icon' => 'fa-solid fa-shield-halved', 'description' => 'Hardening, performance tuning, monitoring and ongoing support after launch.'],
        ]);

        $this->seed(Skill::class, [
            ['name' => 'Laravel', 'category' => 'Backend', 'level' => 95],
            ['name' => 'PHP', 'category' => 'Backend', 'level' => 92],
            ['name' => 'PostgreSQL', 'category' => 'Backend', 'level' => 85],
            ['name' => 'Node.js', 'category' => 'Backend', 'level' => 78],
            ['name' => 'JavaScript', 'category' => 'Frontend', 'level' => 90],
            ['name' => 'Vue / React', 'category' => 'Frontend', 'level' => 84],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend', 'level' => 90],
            ['name' => 'Figma', 'category' => 'Design', 'level' => 80],
            ['name' => 'Docker', 'category' => 'Tools', 'level' => 75],
            ['name' => 'Git', 'category' => 'Tools', 'level' => 90],
        ]);

        $this->seed(Experience::class, [
            ['role' => 'Senior Full-Stack Developer', 'company' => 'Freelance', 'period' => '2022 — Sekarang', 'description' => 'Membangun website, dashboard dan aplikasi untuk klien lokal & internasional.'],
            ['role' => 'Backend Engineer', 'company' => 'Tech Company', 'period' => '2020 — 2022', 'description' => 'Merancang API berskala besar, integrasi payment gateway dan optimasi database.'],
            ['role' => 'Web Developer', 'company' => 'Digital Agency', 'period' => '2018 — 2020', 'description' => 'Mengembangkan company profile, e-commerce dan sistem informasi.'],
        ]);

        $this->seed(Testimonial::class, [
            ['name' => 'Andi Pratama', 'position' => 'CEO, StartupID', 'quote' => 'Hasil kerja sangat rapi, cepat, dan komunikatif. Website kami jadi jauh lebih cepat dan konversi naik.'],
            ['name' => 'Sarah Wijaya', 'position' => 'Product Manager', 'quote' => 'Paham kebutuhan bisnis, bukan cuma coding. Dashboard yang dibuat sangat membantu tim kami.'],
            ['name' => 'Michael Tan', 'position' => 'Founder, Tanverse', 'quote' => 'Deliver tepat waktu dengan kualitas di atas ekspektasi. Pasti kerja sama lagi.'],
        ]);

        $this->seed(SocialLink::class, [
            ['url' => 'https://github.com/rendyirawann', 'platform' => SocialPlatform::detect('https://github.com/rendyirawann')],
            ['url' => 'https://linkedin.com/in/rendyirawann', 'platform' => SocialPlatform::detect('https://linkedin.com/in/rendyirawann')],
            ['url' => 'https://instagram.com/rendyirawann', 'platform' => SocialPlatform::detect('https://instagram.com/rendyirawann')],
            ['url' => 'https://wa.me/628123456789', 'platform' => SocialPlatform::detect('https://wa.me/628123456789')],
        ]);

        if (Project::count() === 0) {
            $web = ProjectCategory::firstOrCreate(['slug' => 'web-app'], ['name' => 'Web App', 'sort_order' => 1]);
            $ui = ProjectCategory::firstOrCreate(['slug' => 'ui-ux'], ['name' => 'UI / UX', 'sort_order' => 2]);
            $product = ProjectCategory::firstOrCreate(['slug' => 'product'], ['name' => 'Product', 'sort_order' => 3]);

            $samples = [
                ['E-Commerce Platform', $web, 'project', 'Laravel, Vue, PostgreSQL, Midtrans', true],
                ['AI Customer Support', $product, 'product', 'Laravel, OpenAI, Redis', true],
                ['Fintech Dashboard', $ui, 'project', 'Figma, Tailwind, Chart.js', true],
                ['School Management System', $web, 'project', 'Laravel, MySQL, Livewire', false],
                ['Booking Mobile App', $ui, 'project', 'Flutter, Laravel API', false],
                ['SaaS Starter Kit', $product, 'product', 'Laravel, Octane, Reverb', false],
            ];

            foreach ($samples as $i => [$title, $category, $type, $stack, $featured]) {
                $n = $i + 1;
                $project = Project::create([
                    'project_category_id' => $category->id,
                    'type' => $type,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'summary' => 'Case study ' . strtolower($title) . ' — dari riset, desain, hingga rilis produksi.',
                    'description' => "Latar belakang\nKlien membutuhkan solusi yang cepat, aman, dan mudah dikelola.\n\nSolusi\nSaya merancang arsitektur, membangun antarmuka, dan mengoptimasi performa hingga skor Lighthouse 95+.\n\nHasil\nWaktu muat turun 60% dan konversi meningkat signifikan.",
                    'cover_image' => "assets/media/stock/600x400/img-{$n}.jpg",
                    'client' => 'Klien ' . $n,
                    'year' => (string) (2026 - $i % 3),
                    'tech_stack' => $stack,
                    'is_featured' => $featured,
                    'is_published' => true,
                    'sort_order' => $n,
                ]);

                foreach ([$n, $n + 6, $n + 12] as $s => $img) {
                    $project->images()->create([
                        'path' => 'assets/media/stock/600x400/img-' . (($img - 1) % 20 + 1) . '.jpg',
                        'caption' => 'Screenshot ' . ($s + 1),
                        'width' => 600,
                        'height' => 400,
                        'sort_order' => $s,
                    ]);
                }

                $project->links()->create(['label' => 'Live Demo', 'url' => 'https://example.com', 'sort_order' => 0]);
            }
        }

        PortfolioCache::flush();
    }

    /** @param class-string $model */
    private function seed(string $model, array $rows): void
    {
        if ($model::count() > 0) {
            return;
        }

        foreach ($rows as $i => $row) {
            $model::create($row + ['sort_order' => $i + 1, 'is_active' => true]);
        }
    }
}
