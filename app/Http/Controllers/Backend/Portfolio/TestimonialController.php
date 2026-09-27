<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Models\Portfolio\Testimonial;

class TestimonialController extends ResourceController
{
    protected string $model = Testimonial::class;
    protected string $route = 'pf.testimonials';
    protected string $title = 'Testimoni';
    protected string $singular = 'Testimoni';
    protected string $icon = 'ki-message-text-2';
    protected string $description = 'Ulasan klien.';
    protected ?string $previewTarget = '#testimonials';
    protected string $searchColumn = 'name';

    protected function fields(): array
    {
        return [
            'name' => ['label' => 'Nama', 'type' => 'text', 'rules' => ['required', 'string', 'max:80'], 'col' => 6],
            'position' => ['label' => 'Jabatan / Perusahaan', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:100'], 'col' => 6],
            'quote' => ['label' => 'Ulasan', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:1000']],
            'avatar' => ['label' => 'Foto (opsional)', 'type' => 'image'],
            'is_active' => ['label' => 'Tampilkan', 'type' => 'toggle'],
        ];
    }

    protected function columns(): array
    {
        return ['avatar' => 'Foto', 'name' => 'Nama', 'position' => 'Jabatan', 'is_active' => 'Status'];
    }
}
