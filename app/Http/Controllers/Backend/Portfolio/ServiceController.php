<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\Service;

class ServiceController extends ResourceController
{
    protected string $model = Service::class;
    protected string $route = 'pf.services';
    protected string $title = 'Layanan';
    protected string $singular = 'Layanan';
    protected string $icon = 'ki-briefcase';
    protected string $description = 'Jasa yang Anda tawarkan.';
    protected ?string $previewTarget = '#services';
    protected array $guide = [
        'Judul = nama layanan singkat, mis. "Project Planning".',
        'Ikon: class Font Awesome, mis. fa-solid fa-diagram-project — pratinjau ikon muncul di sebelah kolom.',
        'Deskripsi 1–2 kalimat tentang manfaatnya untuk klien.',
    ];
    protected string $searchColumn = 'title';

    protected function fields(): array
    {
        return [
            'title' => ['label' => 'Judul', 'type' => 'text', 'rules' => ['required', 'string', 'max:80'], 'col' => 6],
            'icon' => ['label' => 'Ikon', 'type' => 'icon', 'rules' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9\- ]+$/'], 'col' => 6,
                'help' => 'Class Font Awesome, mis. fa-solid fa-code (lihat fontawesome.com/icons).'],
            'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:600']],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['icon' => 'Ikon', 'title' => 'Judul', 'is_active' => 'Status'];
    }
}
