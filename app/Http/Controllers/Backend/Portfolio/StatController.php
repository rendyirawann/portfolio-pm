<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\Stat;

class StatController extends ResourceController
{
    protected string $model = Stat::class;
    protected string $route = 'pf.stats';
    protected string $title = 'Statistik (HUD)';
    protected string $singular = 'Statistik';
    protected string $icon = 'ki-chart-simple';
    protected string $description = 'Angka pencapaian ala HUD game yang tampil di hero dan profil.';
    protected ?string $previewTarget = '#about';
    protected array $guide = [
        'Nilai = angka singkat, mis. 07, 60+, 99%.',
        'Label = keterangannya, mis. "Years Experience".',
        'Dua statistik pertama tampil di hero; semuanya tampil di seksi profil.',
    ];
    protected string $searchColumn = 'label';

    protected function fields(): array
    {
        return [
            'value' => ['label' => 'Nilai', 'type' => 'text', 'rules' => ['required', 'string', 'max:20'], 'col' => 6, 'help' => 'Contoh: 50+, 6,197, 99%'],
            'label' => ['label' => 'Label', 'type' => 'text', 'rules' => ['required', 'string', 'max:60'], 'col' => 6],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['value' => 'Nilai', 'label' => 'Label', 'is_active' => 'Status'];
    }
}
