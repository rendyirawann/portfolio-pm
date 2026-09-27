<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\FooterLink;

class FooterLinkController extends ResourceController
{
    protected string $model = FooterLink::class;
    protected string $route = 'pf.footer';
    protected string $title = 'Link Footer';
    protected string $singular = 'Link footer';
    protected string $icon = 'ki-row-horizontal';
    protected string $description = 'Isi kolom "Navigasi" dan "Kontak" di footer website. Ikon kontak otomatis dari link (email, WhatsApp, dll).';
    protected ?string $previewTarget = '#footer';
    protected string $searchColumn = 'label';

    protected function fields(): array
    {
        return [
            'column' => ['label' => 'Kolom', 'type' => 'select', 'options' => FooterLink::COLUMNS, 'col' => 6,
                'rules' => ['required', 'in:' . implode(',', array_keys(FooterLink::COLUMNS))]],
            'label' => ['label' => 'Teks', 'type' => 'text', 'rules' => ['required', 'string', 'max:120'], 'col' => 6],
            'url' => ['label' => 'Link (opsional)', 'type' => 'text', 'col' => 6,
                'rules' => ['nullable', 'string', 'max:300', 'regex:/^(#[\w-]*|\/[\w\-\/#?=&.]*|https?:\/\/\S+|mailto:\S+@\S+|tel:\+?[\d\s\-]+)$/i'],
                'help' => '#projects, /projects, https://..., mailto:email@domain.com, tel:+62... — kosongkan untuk teks biasa (mis. alamat).'],
            'icon' => ['label' => 'Ikon (opsional)', 'type' => 'icon', 'col' => 6,
                'rules' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9\- ]+$/'],
                'help' => 'Kosongkan = otomatis. Contoh manual: fa-solid fa-phone'],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['column' => 'Kolom', 'label' => 'Teks', 'url' => 'Link', 'is_active' => 'Status'];
    }
}
