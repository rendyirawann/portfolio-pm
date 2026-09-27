<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\ProjectCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectCategoryController extends ResourceController
{
    protected string $model = ProjectCategory::class;
    protected string $route = 'pf.categories';
    protected string $title = 'Kategori Project';
    protected string $singular = 'Kategori';
    protected string $icon = 'ki-category';
    protected string $description = 'Dipakai sebagai filter di daftar project.';
    protected ?string $previewTarget = '#projects';
    protected array $guide = [
        'Nama kategori, mis. "Web App", "Mobile", "Infrastruktur".',
        'Kategori tampil sebagai tombol filter di daftar project & dropdown Category di navbar.',
        'Kategori tanpa project yang dipublikasikan tidak ditampilkan.',
    ];
    protected string $searchColumn = 'name';

    protected function fields(): array
    {
        return [
            'name' => ['label' => 'Nama', 'type' => 'text', 'rules' => ['required', 'string', 'max:60'], 'col' => 6],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['name' => 'Nama', 'slug' => 'Slug', 'is_active' => 'Status'];
    }

    protected function payload(Request $request, Model $item): array
    {
        $data = parent::payload($request, $item);
        $base = Str::slug($data['name']) ?: 'kategori';
        $slug = $base;

        for ($i = 2; ProjectCategory::where('slug', $slug)->whereKeyNot($item->id ?? 0)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $data + ['slug' => $slug];
    }
}
