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
    protected array $guide = [
        'Posisi & perusahaan, mis. "Project Manager" di "PT Contoh".',
        'Periode bebas, mis. "2022 — Sekarang".',
        'Deskripsi: pencapaian utama (angka/hasil lebih meyakinkan).',
        'Foto opsional: dipakai sebagai penanda timeline & tampil di modal detail.',
    ];
    protected string $searchColumn = 'role';

    protected function fields(): array
    {
        return [
            'role' => ['label' => 'Posisi', 'type' => 'text', 'rules' => ['required', 'string', 'max:100'], 'col' => 6],
            'company' => ['label' => 'Perusahaan / Instansi', 'type' => 'text', 'rules' => ['required', 'string', 'max:100'], 'col' => 6],
            'period' => ['label' => 'Periode', 'type' => 'text', 'rules' => ['required', 'string', 'max:60'], 'col' => 6, 'help' => 'Mis. 2022 — Sekarang'],
            'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:1500']],
            'image' => ['label' => 'Foto tempat kerja (opsional)', 'type' => 'image', 'help' => 'Dipasang di dalam penanda timeline. Kalau kosong, penandanya tetap belah ketupat merah polos.'],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['image' => 'Foto', 'role' => 'Posisi', 'company' => 'Perusahaan', 'period' => 'Periode', 'is_active' => 'Status'];
    }
}
