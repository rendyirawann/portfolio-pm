<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Http\Controllers\Controller;
use App\Models\Portfolio\Project;
use App\Models\Portfolio\ProjectCategory;
use App\Support\ActivityRecorder;
use App\Support\Portfolio\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Projects / products (parent) with their gallery images, downloadable
 * files and external links (children).
 */
class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $category = $request->integer('category') ?: null;
        $type = $request->query('type');

        $projects = Project::query()
            ->with('category:id,name')
            ->with(['images' => fn ($q) => $q->select('id', 'project_id', 'path', 'thumb_path')->limit(1)])
            ->withCount(['images', 'files', 'links'])
            ->when($search !== '', fn ($q) => $q->where('title', 'ilike', '%' . addcslashes($search, '%_\\') . '%'))
            ->when($category, fn ($q) => $q->where('project_category_id', $category))
            ->when(isset(Project::TYPES[$type]), fn ($q) => $q->where('type', $type))
            ->orderBy('sort_order')->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('backend.portfolio.project.index', [
            'projects' => $projects,
            'categories' => ProjectCategory::ordered()->get(['id', 'name']),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return $this->form(new Project([
            'is_published' => true,
            'type' => 'project',
            'sort_order' => (Project::max('sort_order') ?? 0) + 1,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $project = new Project();
        $this->persist($request, $project);

        ActivityRecorder::created('portfolio', 'Menambah project "' . $project->title . '"', $project->toArray(), $project);

        return redirect()->route('pf.projects.edit', $project)->with('success', 'Project berhasil ditambahkan.');
    }

    public function show(Project $project): RedirectResponse
    {
        return redirect()->route('pf.projects.edit', $project);
    }

    public function edit(Project $project): View
    {
        return $this->form($project->load(['images', 'files', 'links']));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $before = $project->toArray();
        $this->persist($request, $project);

        ActivityRecorder::updated('portfolio', 'Mengubah project "' . $project->title . '"', $before, $project->fresh()->toArray(), $project);

        return redirect()->route('pf.projects.edit', $project)->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $before = $project->toArray();
        $project->load(['images', 'files'])->delete();

        ActivityRecorder::deleted('portfolio', 'Menghapus project "' . $before['title'] . '"', $before);

        return redirect()->route('pf.projects.index')->with('success', 'Project berhasil dihapus.');
    }

    // ------------------------------------------------------------------

    private function form(Project $project): View
    {
        return view('backend.portfolio.project.form', [
            'project' => $project,
            'categories' => ProjectCategory::ordered()->get(['id', 'name']),
            'maxKb' => Media::MAX_KB,
        ]);
    }

    private function persist(Request $request, Project $project): void
    {
        $max = 'max:' . Media::MAX_KB;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:170', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($project->id)],
            'type' => ['required', Rule::in(array_keys(Project::TYPES))],
            'project_category_id' => ['nullable', 'integer', 'exists:project_categories,id'],
            'summary' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:20000'],
            'client' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'string', 'max:10'],
            'tech_stack' => ['nullable', 'string', 'max:300'],
            'meta_title' => ['nullable', 'string', 'max:150'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],

            'cover' => ['nullable', 'image', 'mimes:' . Media::IMAGE_MIMES, $max],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:' . Media::IMAGE_MIMES, $max],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'mimes:' . Media::FILE_MIMES, $max],

            'existing_images' => ['nullable', 'array'],
            'existing_images.*.caption' => ['nullable', 'string', 'max:150'],
            'existing_images.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer'],
            'delete_files' => ['nullable', 'array'],
            'delete_files.*' => ['integer'],

            'links' => ['nullable', 'array', 'max:20'],
            'links.*.label' => ['nullable', 'string', 'max:60'],
            'links.*.url' => ['nullable', 'url:http,https', 'max:300'],
        ], [
            'images.*.max' => 'Setiap gambar maksimal 10 MB.',
            'files.*.max' => 'Setiap file maksimal 10 MB.',
            'images.*.image' => 'File galeri harus berupa gambar.',
            'files.*.mimes' => 'Tipe file tidak diizinkan.',
        ]);

        // Do the slow image work before opening the transaction.
        $cover = $request->hasFile('cover') ? Media::storeImage($request->file('cover'), 'projects', 1600, null) : null;
        $gallery = collect($request->file('images', []))->map(fn ($f) => Media::storeImage($f, 'projects'));
        $files = collect($request->file('files', []))->map(fn ($f) => Media::storeFile($f, 'project-files'));

        DB::transaction(function () use ($request, $project, $data, $cover, $gallery, $files) {
            $old = ['cover' => $project->cover_image];

            $project->fill([
                'title' => $data['title'],
                'slug' => ($data['slug'] ?? null) ?: ($project->slug ?: null),
                'type' => $data['type'],
                'project_category_id' => $data['project_category_id'] ?? null,
                'summary' => $data['summary'] ?? null,
                'description' => $data['description'] ?? null,
                'client' => $data['client'] ?? null,
                'year' => $data['year'] ?? null,
                'tech_stack' => $data['tech_stack'] ?? null,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'is_featured' => $request->boolean('is_featured'),
                'is_published' => $request->boolean('is_published'),
            ]);

            if ($cover) {
                $project->cover_image = $cover['path'];
                Media::delete($old['cover']);
            } elseif ($request->boolean('remove_cover')) {
                Media::delete($old['cover']);
                $project->cover_image = null;
            }

            $project->save();

            // --- Existing gallery: captions / order / removal ---
            $deleteImages = array_map('intval', $data['delete_images'] ?? []);
            foreach ($project->images()->get() as $image) {
                if (in_array($image->id, $deleteImages, true)) {
                    Media::delete($image->path, $image->thumb_path);
                    $image->delete();
                    continue;
                }

                if ($meta = $data['existing_images'][$image->id] ?? null) {
                    $image->update([
                        'caption' => $meta['caption'] ?? null,
                        'sort_order' => (int) ($meta['sort_order'] ?? $image->sort_order),
                    ]);
                }
            }

            $next = (int) $project->images()->max('sort_order') + 1;
            foreach ($gallery as $i => $img) {
                $project->images()->create([
                    'path' => $img['path'],
                    'thumb_path' => $img['thumb'],
                    'width' => $img['width'],
                    'height' => $img['height'],
                    'sort_order' => $next + $i,
                ]);
            }

            // --- Files ---
            $deleteFiles = array_map('intval', $data['delete_files'] ?? []);
            $project->files()->whereIn('id', $deleteFiles)->get()->each(function ($file) {
                Media::delete($file->path);
                $file->delete();
            });

            $next = (int) $project->files()->max('sort_order') + 1;
            foreach ($files as $i => $file) {
                $project->files()->create($file + ['sort_order' => $next + $i]);
            }

            // --- Links: the submitted list replaces the stored one ---
            $project->links()->delete();
            foreach (array_values($data['links'] ?? []) as $i => $link) {
                if (filled($link['url'] ?? null)) {
                    $project->links()->create([
                        'label' => ($link['label'] ?? null) ?: 'Link',
                        'url' => $link['url'],
                        'sort_order' => $i,
                    ]);
                }
            }
        });
    }
}
