<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\Experience;

class ExperienceController extends ResourceController
{
    protected string $model = Experience::class;
    protected string $route = 'pf.experiences';
    protected string $title = 'Pengalaman';
    protected string $singular = 'Pengalaman';
    protected string $icon = 'ki-medal-star';
    protected string $description = 'Riwayat kerja / pendidikan, tampil sebagai timeline.';
    protected ?string $previewTarget = '#experience';
    protected string $searchColumn = 'role';

    protected function fields(): array
    {
        return [
            'role' => ['label' => 'Posisi', 'type' => 'text', 'rules' => ['required', 'string', 'max:100'], 'col' => 6],
            'company' => ['label' => 'Perusahaan / Instansi', 'type' => 'text', 'rules' => ['required', 'string', 'max:100'], 'col' => 6],
            'period' => ['label' => 'Periode', 'type' => 'text', 'rules' => ['required', 'string', 'max:60'], 'col' => 6, 'help' => 'Mis. 2022 — Sekarang'],
            'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:1500']],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['role' => 'Posisi', 'company' => 'Perusahaan', 'period' => 'Periode', 'is_active' => 'Status'];
    }
}
