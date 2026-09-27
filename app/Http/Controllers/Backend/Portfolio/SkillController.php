<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\Skill;

class SkillController extends ResourceController
{
    protected string $model = Skill::class;
    protected string $route = 'pf.skills';
    protected string $title = 'Keahlian';
    protected string $singular = 'Keahlian';
    protected string $icon = 'ki-technology-2';
    protected string $description = 'Skill beserta levelnya (0–100). Kategori mengelompokkan skill di halaman depan.';
    protected ?string $previewTarget = '#skills';
    protected string $searchColumn = 'name';

    protected function fields(): array
    {
        return [
            'name' => ['label' => 'Nama skill', 'type' => 'text', 'rules' => ['required', 'string', 'max:60'], 'col' => 6],
            'category' => ['label' => 'Kategori', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:40'], 'col' => 6, 'help' => 'Mis. Frontend, Backend, Tools'],
            'level' => ['label' => 'Level (0–100)', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0', 'max:100'], 'col' => 6],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['name' => 'Skill', 'category' => 'Kategori', 'level' => 'Level', 'is_active' => 'Status'];
    }
}
