@php
    use App\Support\Portfolio\Media;
    use Illuminate\Support\Str;

    $photo = Media::url($c['about_photo'], 'assets/front/img/profile-default.svg');
    $wa = preg_replace('/\D/', '', (string) $c['contact_whatsapp']);
    $web = preg_replace('#^https?://#', '', rtrim(url('/'), '/'));
    $linkedin = $c['contact_linkedin'] ? preg_replace('#^https?://(www\.)?#', '', rtrim($c['contact_linkedin'], '/')) : null;
    $paragraphs = fn (?string $text, int $limit) => collect(preg_split("/\R{2,}/", trim((string) $text)))
        ->filter()->map(fn ($p) => trim(preg_replace('/\s+/', ' ', $p)))->implode("\n\n");
    // Jumlah lembar berbeda tiap versi:
    //   portfolio      : 1 lembar profil + 1 lembar PER project (bergambar)
    //   portfolio-lite : 1 lembar profil + daftar project yang dipadatkan
    //   cv / resume    : lembar tetap, project jadi daftar
    $pages = match ($variant) {
        'portfolio' => 1 + $projects->count(),
        'portfolio-lite' => 1 + (int) ceil(max($projects->count(), 1) / 4),
        'cv' => 2,
        default => 1,
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $variantLabel }} — {{ $c['about_name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    @include('backend.portfolio._export_styles')
</head>
<body>
    <div class="toolbar">
        <span class="toolbar__title">{{ $variantLabel }} — {{ $c['about_name'] }}</span>
        <span class="toolbar__hint">± {{ $pages }} halaman A4 · Klik "Download PDF", lalu pilih <b>Save as PDF</b> / <b>Simpan sebagai PDF</b>.</span>
        <div class="toolbar__actions">
            <a class="btn" href="{{ route('pf.export') }}">Semua versi</a>
            <button class="btn btn--primary" type="button" id="print-btn">Download PDF</button>
        </div>
    </div>

    @include('backend.portfolio._export_' . str_replace('-', '_', $variant))
    <script>
        document.getElementById('print-btn').addEventListener('click', function () { window.print(); });
    </script>
</body>
</html>
