<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Http\Controllers\Controller;
use App\Models\Portfolio\PageContent;
use App\Models\Portfolio\Project;
use App\Support\Portfolio\PortfolioData;
use App\Support\ActivityRecorder;
use App\Support\Portfolio\ContentSchema;
use App\Support\Portfolio\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Edits every single-value text/image on the portfolio (ContentSchema). */
class PageContentController extends Controller
{
    public function index(): View
    {
        return view('backend.portfolio.content', [
            'groups' => ContentSchema::groups(),
            'values' => PageContent::query()->pluck('value', 'key')->all() + ContentSchema::defaults(),
        ]);
    }

    /**
     * Print-ready A4 portfolio built from the live website content:
     * profile, skills, experience and the top three projects.
     */
    /**
     * Keempat versi export. Perbedaannya bukan sekadar gaya:
     *
     *  portfolio       etalase karya — satu lembar per project lengkap dengan galeri.
     *  portfolio-lite  isi yang sama tapi TANPA gambar; daftar + detail teks saja.
     *  cv              riwayat lengkap & kronologis, tidak dipangkas.
     *  resume          intisari satu halaman untuk melamar.
     */
    public const EXPORT_VARIANTS = [
        'portfolio' => [
            'label' => 'Portfolio',
            'tagline' => 'Etalase karya, bergambar',
            'desc' => 'Satu lembar profil, lalu satu lembar penuh untuk setiap project/produk lengkap dengan gambar sampul dan galerinya. Versi paling tebal — untuk dipresentasikan ke klien.',
        ],
        'portfolio-lite' => [
            'label' => 'Portfolio Ringkas',
            'tagline' => 'Daftar & detail, tanpa gambar',
            'desc' => 'Isi yang sama dengan Portfolio, tetapi project disajikan sebagai daftar bernomor berisi ringkasan, detail, teknologi, dan tautan — tanpa gambar sama sekali. Berkasnya jauh lebih kecil, enak dikirim lewat email.',
        ],
        'cv' => [
            'label' => 'CV',
            'tagline' => 'Riwayat lengkap & kronologis',
            'desc' => 'Curriculum Vitae: seluruh pengalaman dengan deskripsi utuh, semua keahlian beserta levelnya, bidang layanan, tabel seluruh karya, dan referensi. Tidak ada yang dipangkas.',
        ],
        'resume' => [
            'label' => 'Resume',
            'tagline' => 'Intisari satu halaman',
            'desc' => 'Ringkas dan selektif: profil singkat, keahlian teratas, tiga pengalaman terbaru, dan karya unggulan — dirancang muat satu halaman untuk dilampirkan saat melamar.',
        ],
    ];

    /** Menu export: memilih versi sekaligus melihat pratinjaunya. */
    public function export(): View
    {
        return view('backend.portfolio.export-index', [
            'variants' => self::EXPORT_VARIANTS,
            'active' => 'portfolio',
        ]);
    }

    /** Lembar cetak untuk satu versi. */
    public function exportSheet(string $variant = 'portfolio'): View
    {
        abort_unless(isset(self::EXPORT_VARIANTS[$variant]), 404);

        $layout = PortfolioData::layout();
        $home = PortfolioData::home();

        // TANPA limit: dulu di sini ada ->limit(3) sehingga hanya 3 project yang
        // ikut tercetak, dan galerinya dipotong 3 gambar.
        $projects = Project::visible()
            ->with(['category:id,name', 'images', 'links'])
            ->get();

        return view('backend.portfolio.export', [
            'variant' => $variant,
            'variantLabel' => self::EXPORT_VARIANTS[$variant]['label'],
            'c' => $layout['content'],
            'socials' => $layout['socials'],
            'stats' => $home['stats'],
            'skills' => $home['skills'],
            'services' => $home['services'],
            'experiences' => $home['experiences'],
            'testimonials' => $home['testimonials'],
            'projects' => $projects,
        ]);
    }

    /** The sign-in screen rendered for the admin's live preview panel. */
    public function loginPreview(): View
    {
        return view('auth.login', ['pfPreview' => true]);
    }

    public function update(Request $request): RedirectResponse
    {
        $group = (string) $request->input('_group');
        $groups = ContentSchema::groups();
        abort_unless(isset($groups[$group]), 404);

        $fields = $groups[$group]['fields'];
        $validated = $request->validate($this->rules($fields));
        $current = PageContent::query()->whereIn('key', array_keys($fields))->pluck('value', 'key')->all();

        foreach ($fields as $key => [, $type]) {
            $value = match ($type) {
                'toggle' => $request->boolean($key) ? '1' : '0',
                'image', 'file' => $this->upload($request, $key, $type, $current[$key] ?? null),
                default => $validated[$key] ?? '',
            };

            if ($value === false) {
                continue; // file field left untouched
            }

            PageContent::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        ActivityRecorder::updated('portfolio', 'Memperbarui konten halaman: ' . $groups[$group]['label'], $current,
            PageContent::query()->whereIn('key', array_keys($fields))->pluck('value', 'key')->all());

        return redirect()->route('pf.content.index', ['tab' => $group])
            ->with('success', 'Konten "' . $groups[$group]['label'] . '" berhasil disimpan.');
    }

    private function rules(array $fields): array
    {
        $rules = [];

        foreach ($fields as $key => $field) {
            [, $type] = $field;
            $rules[$key] = match ($type) {
                'textarea' => ['nullable', 'string', 'max:2000'],
                'url' => ['nullable', 'url:http,https', 'max:300'],
                'email' => ['nullable', 'email', 'max:150'],
                'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'toggle' => ['nullable', 'boolean'],
                'select' => ['nullable', Rule::in($field[3] ?? [])],
                'image' => ['nullable', 'image', 'mimes:' . Media::IMAGE_MIMES, 'max:' . Media::MAX_KB],
                'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:' . Media::MAX_KB],
                default => ['nullable', 'string', 'max:300'],
            };
        }

        return $rules;
    }

    /** New path, null when removed, false when untouched. */
    private function upload(Request $request, string $key, string $type, ?string $current): string|null|false
    {
        if ($request->hasFile($key)) {
            Media::delete($current);

            return $type === 'image'
                ? Media::storeImage($request->file($key), 'content', 2000, null)['path']
                : Media::storeFile($request->file($key), 'content')['path'];
        }

        if ($request->boolean("remove_{$key}")) {
            Media::delete($current);

            return null;
        }

        return false;
    }
}
