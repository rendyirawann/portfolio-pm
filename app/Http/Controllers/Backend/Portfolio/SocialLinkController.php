<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\SocialLink;

class SocialLinkController extends ResourceController
{
    protected string $model = SocialLink::class;
    protected string $route = 'pf.socials';
    protected string $title = 'Sosial Media';
    protected string $singular = 'Sosial media';
    protected string $icon = 'ki-share';
    protected string $description = 'Cukup tempel link-nya — ikon otomatis mengikuti platform (GitHub, Instagram, LinkedIn, WhatsApp, dst).';
    protected ?string $previewTarget = '#contact';
    protected array $guide = [
        'Tempel link profil lengkap, mis. https://instagram.com/nama.',
        'Ikon terdeteksi otomatis dari link (lihat pratinjau ikon).',
        'Tampil di hero, kontak, footer website, dan footer admin.',
    ];
    protected string $searchColumn = 'url';

    protected function fields(): array
    {
        return [
            'url' => ['label' => 'Link', 'type' => 'social', 'col' => 6,
                'rules' => ['required', 'string', 'max:300', 'regex:/^(https?:\/\/\S+|mailto:\S+@\S+|tel:\+?[\d\s\-]+)$/i'],
                'help' => 'https://..., mailto:email@domain.com, atau tel:+62...'],
            'label' => ['label' => 'Label (opsional)', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:60'], 'col' => 6,
                'help' => 'Kosongkan untuk memakai nama platform.'],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['platform' => 'Ikon', 'url' => 'Link', 'label' => 'Label', 'is_active' => 'Status'];
    }
}
