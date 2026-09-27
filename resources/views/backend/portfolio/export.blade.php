@php
    use App\Support\Portfolio\Media;
    use Illuminate\Support\Str;

    $photo = Media::url($c['about_photo'], 'assets/front/img/profile-default.svg');
    $wa = preg_replace('/\D/', '', (string) $c['contact_whatsapp']);
    $web = preg_replace('#^https?://#', '', rtrim(url('/'), '/'));
    $linkedin = $c['contact_linkedin'] ? preg_replace('#^https?://(www\.)?#', '', rtrim($c['contact_linkedin'], '/')) : null;
    $paragraphs = fn (?string $text, int $limit) => collect(preg_split("/\R{2,}/", trim((string) $text)))
        ->filter()->map(fn ($p) => trim(preg_replace('/\s+/', ' ', $p)))->implode("\n\n");
    $pages = 1 + $projects->count();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Portfolio — {{ $c['about_name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <style>
        :root {
            --ink: #0f172a;
            --text: #1e293b;
            --muted: #64748b;
            --line: #e2e8f0;
            --soft: #f1f5f9;
            --accent: #c8102e;
            --accent-soft: #fdecef;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body { background: #e5e7eb; color: var(--text); font: 400 10pt/1.55 'Plus Jakarta Sans', 'Segoe UI', Arial, sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        h1, h2, h3, h4, p { margin: 0; }
        ul { margin: 0; padding: 0; list-style: none; }
        a { color: inherit; text-decoration: none; }

        /* ---------- Screen toolbar ---------- */
        .toolbar { position: sticky; top: 0; z-index: 5; display: flex; align-items: center; gap: 12px; padding: 12px 24px; background: var(--ink); color: #fff; font-size: 13px; }
        .toolbar__title { font-weight: 700; }
        .toolbar__hint { color: #94a3b8; }
        .toolbar__actions { margin-left: auto; display: flex; gap: 8px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px; border-radius: 8px; border: 1px solid #334155; background: transparent; color: #fff; font: 600 13px 'Plus Jakarta Sans', sans-serif; cursor: pointer; }
        .btn--primary { background: var(--accent); border-color: var(--accent); }

        /* ---------- A4 sheets ---------- */
        .sheet { position: relative; width: 210mm; height: 297mm; margin: 24px auto; background: #fff; box-shadow: 0 10px 40px rgba(15, 23, 42, .15); overflow: hidden; display: flex; flex-direction: column; }
        .sheet__body { flex: 1; padding: 14mm 16mm 0; display: flex; flex-direction: column; min-height: 0; }
        .sheet__foot { display: flex; justify-content: space-between; padding: 6mm 16mm 8mm; font-size: 7.5pt; color: var(--muted); letter-spacing: .02em; }
        .sheet__foot b { color: var(--ink); font-weight: 600; }

        .eyebrow { font-size: 7.5pt; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--accent); }
        .section-title { display: flex; align-items: center; gap: 10px; margin: 0 0 4mm; font-size: 8pt; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--ink); }
        .section-title::after { content: ''; flex: 1; height: 1px; background: var(--line); }

        /* ---------- Page 1: profile ---------- */
        .hero { display: grid; grid-template-columns: 52mm 1fr; background: var(--ink); color: #fff; }
        .hero__photo { height: 64mm; background: #1e293b; overflow: hidden; display: flex; align-items: flex-end; justify-content: center; }
        .hero__photo img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
        .hero__info { padding: 10mm 12mm; display: flex; flex-direction: column; justify-content: center; gap: 2.5mm; border-left: 3px solid var(--accent); }
        .hero__name { font-size: 26pt; font-weight: 800; letter-spacing: -.02em; line-height: 1.05; }
        .hero__role { font-size: 11pt; font-weight: 500; color: #cbd5e1; }
        .hero__contact { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5mm 6mm; margin-top: 4mm; font-size: 8.5pt; color: #e2e8f0; }
        .hero__contact span { display: flex; gap: 6px; }
        .hero__contact em { font-style: normal; color: #94a3b8; min-width: 16mm; }

        .intro { padding: 9mm 16mm 0; }
        .intro p { font-size: 10.5pt; line-height: 1.7; color: var(--text); }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 4mm; margin-top: 6mm; }
        .stats li { padding: 3.5mm 4mm; background: var(--soft); border-radius: 2mm; border-left: 2px solid var(--accent); }
        .stats b { display: block; font-size: 15pt; font-weight: 800; color: var(--ink); line-height: 1.1; }
        .stats span { font-size: 7.5pt; color: var(--muted); }

        .cols { display: grid; grid-template-columns: 1fr 1.15fr; gap: 10mm; padding: 9mm 16mm 0; flex: 1; min-height: 0; overflow: hidden; }
        .skill-group + .skill-group { margin-top: 4mm; }
        .skill-group h4 { font-size: 8pt; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 2mm; }
        .skill { display: grid; grid-template-columns: 1fr 24mm; align-items: center; gap: 4mm; font-size: 9pt; padding: .9mm 0; }
        .skill__bar { height: 1.4mm; background: var(--line); border-radius: 1mm; overflow: hidden; }
        .skill__bar i { display: block; height: 100%; background: var(--ink); }
        .services { margin-top: 7mm; display: grid; gap: 2.5mm; }
        .services li { font-size: 9pt; }
        .services b { color: var(--ink); font-weight: 700; }

        .timeline li { position: relative; padding: 0 0 5mm 6mm; border-left: 1px solid var(--line); }
        .timeline li:last-child { padding-bottom: 0; }
        .timeline li::before { content: ''; position: absolute; left: -1.3mm; top: 1.2mm; width: 2.4mm; height: 2.4mm; border-radius: 50%; background: #fff; border: 2px solid var(--accent); }
        .timeline__period { font-size: 7.5pt; font-weight: 700; color: var(--accent); letter-spacing: .06em; text-transform: uppercase; }
        .timeline h3 { font-size: 10.5pt; font-weight: 700; color: var(--ink); margin-top: .5mm; }
        .timeline__company { font-size: 8.5pt; font-weight: 600; color: var(--muted); }
        .timeline p { font-size: 8.8pt; margin-top: 1mm; }

        /* ---------- Project pages ---------- */
        .project__head { display: flex; justify-content: space-between; align-items: flex-end; gap: 8mm; padding-bottom: 5mm; border-bottom: 1px solid var(--line); }
        .project__num { font-size: 34pt; font-weight: 800; color: var(--line); line-height: .9; }
        .project__title { font-size: 20pt; font-weight: 800; color: var(--ink); letter-spacing: -.01em; line-height: 1.15; margin-top: 1.5mm; }
        .project__cover { margin-top: 6mm; height: 92mm; border-radius: 2.5mm; overflow: hidden; background: var(--soft); }
        .project__cover img { width: 100%; height: 100%; object-fit: cover; }
        .project__grid { display: grid; grid-template-columns: 1fr 52mm; gap: 9mm; margin-top: 7mm; flex: 1; min-height: 0; }
        .project__summary { font-size: 11pt; font-weight: 600; color: var(--ink); line-height: 1.5; }
        .project__desc { margin-top: 4mm; font-size: 9.2pt; line-height: 1.7; white-space: pre-line; color: var(--text); overflow: hidden; }
        .meta { display: grid; gap: 3.5mm; align-content: start; }
        .meta dt { font-size: 7pt; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
        .meta dd { margin: .6mm 0 0; font-size: 9pt; font-weight: 600; color: var(--ink); word-break: break-word; }
        .tags { display: flex; flex-wrap: wrap; gap: 1.5mm; margin-top: 1mm; }
        .tags li { font-size: 7.5pt; font-weight: 600; padding: 1mm 2.4mm; border-radius: 1mm; background: var(--accent-soft); color: var(--accent); }
        .thumbs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 3mm; margin-top: 6mm; }
        .thumbs img { width: 100%; height: 26mm; object-fit: cover; border-radius: 1.5mm; background: var(--soft); }

        @page { size: A4; margin: 0; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { margin: 0; box-shadow: none; page-break-after: always; break-after: page; }
            .sheet:last-child { page-break-after: auto; break-after: auto; }
        }
        @media screen and (max-width: 860px) {
            .sheet { transform-origin: top center; zoom: .45; }
            .toolbar__hint { display: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <span class="toolbar__title">Portfolio — {{ $c['about_name'] }}</span>
        <span class="toolbar__hint">{{ $pages }} halaman A4 · Klik "Download PDF", lalu pilih <b>Save as PDF</b> / <b>Simpan sebagai PDF</b>.</span>
        <div class="toolbar__actions">
            <a class="btn" href="{{ route('dashboard') }}">Kembali</a>
            <button class="btn btn--primary" type="button" id="print-btn">Download PDF</button>
        </div>
    </div>

    {{-- ===================== PAGE 1 — PROFILE ===================== --}}
    <section class="sheet">
        <header class="hero">
            <div class="hero__photo"><img src="{{ $photo }}" alt="{{ $c['about_name'] }}"></div>
            <div class="hero__info">
                <p class="eyebrow">Portfolio {{ now()->year }}</p>
                <h1 class="hero__name">{{ $c['about_name'] }}</h1>
                <p class="hero__role">{{ $c['about_role'] }}</p>
                <div class="hero__contact">
                    @if ($c['contact_email'])<span><em>Email</em>{{ $c['contact_email'] }}</span>@endif
                    @if ($wa)<span><em>WhatsApp</em>+{{ $wa }}</span>@endif
                    @if ($c['about_location'])<span><em>Lokasi</em>{{ $c['about_location'] }}</span>@endif
                    <span><em>Website</em>{{ $web }}</span>
                    @if ($linkedin)<span><em>LinkedIn</em>{{ $linkedin }}</span>@endif
                </div>
            </div>
        </header>

        <div class="intro">
            <h2 class="section-title">Profil</h2>
            <p>{{ $c['about_bio'] }}</p>
            @if ($stats->isNotEmpty())
                <ul class="stats">
                    @foreach ($stats->take(4) as $s)
                        <li><b>{{ $s->value }}</b><span>{{ $s->label }}</span></li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="cols">
            <div>
                <h2 class="section-title">Keahlian</h2>
                @foreach ($skills->take(4) as $group => $items)
                    <div class="skill-group">
                        <h4>{{ $group }}</h4>
                        @foreach ($items->take(5) as $skill)
                            <div class="skill">
                                <span>{{ $skill->name }}</span>
                                <span class="skill__bar"><i style="width: {{ (int) $skill->level }}%"></i></span>
                            </div>
                        @endforeach
                    </div>
                @endforeach


            </div>

            <div>
                <h2 class="section-title">Pengalaman</h2>
                <ul class="timeline">
                    @foreach ($experiences->take(4) as $exp)
                        <li>
                            <span class="timeline__period">{{ $exp->period }}</span>
                            <h3>{{ $exp->role }}</h3>
                            <span class="timeline__company">{{ $exp->company }}</span>
                            @if ($exp->description)<p>{{ Str::limit($exp->description, 180) }}</p>@endif
                        </li>
                    @endforeach
                </ul>

                @if ($services->isNotEmpty())
                    <h2 class="section-title" style="margin-top: 8mm">Layanan</h2>
                    <ul class="services" style="margin-top: 0">
                        @foreach ($services->take(5) as $svc)
                            <li><b>{{ $svc->title }}</b> — {{ Str::limit($svc->description, 70) }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <footer class="sheet__foot"><span><b>{{ $c['about_name'] }}</b> · {{ $c['about_role'] }}</span><span>01 / {{ str_pad($pages, 2, '0', STR_PAD_LEFT) }}</span></footer>
    </section>

    {{-- ===================== PROJECT PAGES ===================== --}}
    @foreach ($projects as $i => $p)
        @php
            $cover = $p->cover_image ? Media::url($p->cover_image) : $p->cover_url;
            $thumbs = $p->images->map(fn ($img) => Media::url($img->thumb_path ?: $img->path))->reject(fn ($u) => $u === $cover)->take(3);
        @endphp
        <section class="sheet">
            <div class="sheet__body">
                <header class="project__head">
                    <div>
                        <p class="eyebrow">Selected {{ \App\Models\Portfolio\Project::TYPES[$p->type] ?? 'Project' }}@if ($p->category) · {{ $p->category->name }}@endif</p>
                        <h2 class="project__title">{{ $p->title }}</h2>
                    </div>
                    <span class="project__num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                </header>

                <figure class="project__cover" style="margin-left: 0; margin-right: 0; margin-bottom: 0"><img src="{{ $cover }}" alt="{{ $p->title }}"></figure>

                <div class="project__grid">
                    <div style="min-height: 0; overflow: hidden">
                        @if ($p->summary)<p class="project__summary">{{ $p->summary }}</p>@endif
                        <p class="project__desc">{{ Str::limit($paragraphs($p->description, 900), 900) }}</p>
                    </div>
                    <dl class="meta" style="margin: 0">
                        @if ($p->client)<div><dt>Klien</dt><dd>{{ $p->client }}</dd></div>@endif
                        @if ($p->year)<div><dt>Tahun</dt><dd>{{ $p->year }}</dd></div>@endif
                        @if ($p->category)<div><dt>Kategori</dt><dd>{{ $p->category->name }}</dd></div>@endif
                        @if ($p->tech_list)
                            <div><dt>Teknologi</dt><dd><ul class="tags">@foreach ($p->tech_list as $t)<li>{{ $t }}</li>@endforeach</ul></dd></div>
                        @endif
                        <div><dt>Detail</dt><dd>{{ $web }}/projects/{{ $p->slug }}</dd></div>
                        @foreach ($p->links->take(2) as $link)
                            <div><dt>{{ $link->label }}</dt><dd>{{ preg_replace('#^https?://#', '', $link->url) }}</dd></div>
                        @endforeach
                    </dl>
                </div>

                @if ($thumbs->isNotEmpty())
                    <div class="thumbs">
                        @foreach ($thumbs as $src)<img src="{{ $src }}" alt="">@endforeach
                    </div>
                @endif
            </div>
            <footer class="sheet__foot"><span><b>{{ $c['about_name'] }}</b> · Selected Work</span><span>{{ str_pad($i + 2, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($pages, 2, '0', STR_PAD_LEFT) }}</span></footer>
        </section>
    @endforeach

    <script>
        document.getElementById('print-btn').addEventListener('click', function () { window.print(); });
    </script>
</body>
</html>
