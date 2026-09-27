<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\NavItem;

class NavItemController extends ResourceController
{
    protected string $model = NavItem::class;
    protected string $route = 'pf.nav';
    protected string $title = 'Menu Navbar';
    protected string $singular = 'Menu';
    protected string $icon = 'ki-burger-menu-2';
    protected string $description = 'Link di navbar halaman depan. Pakai #id-seksi (mis. #projects) atau URL lengkap.';
    protected ?string $previewTarget = '#home';
    protected array $guide = [
        'Label = teks menu yang terlihat, mis. "Projects".',
        'Tujuan: #projects untuk lompat ke seksi, /projects untuk halaman lain, atau URL lengkap.',
        'Urutan kecil tampil paling kiri. Matikan "Tampilkan" untuk menyembunyikan tanpa menghapus.',
    ];
    protected string $searchColumn = 'label';

    protected function fields(): array
    {
        return [
            'label' => ['label' => 'Label', 'type' => 'text', 'rules' => ['required', 'string', 'max:40'], 'col' => 6],
            'target' => ['label' => 'Tujuan', 'type' => 'text', 'col' => 6,
                'rules' => ['required', 'string', 'max:200', 'regex:/^(#[\w-]*|\/[\w\-\/#?=&.]*|https?:\/\/\S+)$/'],
                'help' => 'Contoh: #about, #projects, /projects, https://blog.domain.com'],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['label' => 'Label', 'target' => 'Tujuan', 'is_active' => 'Status'];
    }
}
