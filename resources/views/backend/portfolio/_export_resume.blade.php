{{-- Versi RESUME.
     Ringkas dan selektif — dirancang muat SATU HALAMAN untuk dilampirkan saat
     melamar. Hanya intisari: profil singkat, keahlian teratas, pengalaman
     terbaru, dan karya unggulan. Riwayat selengkapnya ada di versi CV. --}}

@php
    // Yang ditampilkan sengaja dibatasi — inilah pembeda resume dari CV.
    $expTop = $experiences->take(3);
    $skillTop = $skills->map(fn ($items) => $items->sortByDesc('level')->take(4));
    $projTop = $projects->sortByDesc('is_featured')->take(4);
    $bio = \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', (string) $c['about_bio']), 320);
@endphp

<section class="sheet">
    <header class="hero hero--doc">
        <div class="hero__info">
            <p class="eyebrow">Resume</p>
            <h1 class="hero__name">{{ $c['about_name'] }}</h1>
            <p class="hero__role">{{ $c['about_role'] }}</p>
            <div class="hero__contact">
                @if ($c['contact_email'])<span><em>Email</em>{{ $c['contact_email'] }}</span>@endif
                @if ($wa)<span><em>WhatsApp</em>+{{ $wa }}</span>@endif
                @if ($c['about_location'])<span><em>Lokasi</em>{{ $c['about_location'] }}</span>@endif
                <span><em>Website</em>{{ $web }}</span>
            </div>
        </div>
    </header>

    <div class="sheet__body">
        <h2 class="section-title">Ringkasan</h2>
        <p class="doc__lead">{{ $bio }}</p>

        <div class="cols">
            <div>
                <h2 class="section-title doc__gap">Keahlian Inti</h2>
                @foreach ($skillTop as $group => $items)
                    <div class="skill-group">
                        <h4>{{ $group }}</h4>
                        <p class="resume__skills">{{ $items->pluck('name')->implode(' · ') }}</p>
                    </div>
                @endforeach

                @if ($projTop->isNotEmpty())
                    <h2 class="section-title doc__gap">Karya Unggulan</h2>
                    <ul class="resume__proj">
                        @foreach ($projTop as $p)
                            <li>
                                <b>{{ $p->title }}</b>
                                <span>{{ \App\Models\Portfolio\Project::TYPES[$p->type] ?? 'Project' }}@if ($p->year) · {{ $p->year }}@endif</span>
                                @if ($p->summary)<p>{{ \Illuminate\Support\Str::limit($p->summary, 110) }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div>
                <h2 class="section-title doc__gap">Pengalaman Terbaru</h2>
                <ul class="timeline">
                    @foreach ($expTop as $exp)
                        <li>
                            <span class="timeline__period">{{ $exp->period }}</span>
                            <h3>{{ $exp->role }}</h3>
                            <span class="timeline__company">{{ $exp->company }}</span>
                            @if ($exp->description)<p>{{ \Illuminate\Support\Str::limit($exp->description, 150) }}</p>@endif
                        </li>
                    @endforeach
                </ul>

                @if ($stats->isNotEmpty())
                    <ul class="stats resume__stats">
                        @foreach ($stats->take(4) as $s)<li><b>{{ $s->value }}</b><span>{{ $s->label }}</span></li>@endforeach
                    </ul>
                @endif
            </div>
        </div>

        <p class="resume__note">Riwayat lengkap, seluruh keahlian, dan daftar karya menyeluruh tersedia pada versi <b>CV</b> dan <b>Portfolio</b>.</p>
    </div>

    <footer class="sheet__foot">
        <span><b>{{ $c['about_name'] }}</b> · Resume</span>
        <span>01 / 01</span>
    </footer>
</section>
