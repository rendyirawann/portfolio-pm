{{-- Versi CV (Curriculum Vitae).
     Riwayat LENGKAP dan faktual: semua pengalaman dengan deskripsi utuh,
     seluruh keahlian beserta levelnya, layanan, daftar seluruh karya, dan
     referensi. Tidak ada yang dipangkas — itu yang membedakannya dari resume. --}}

<section class="sheet">
    <header class="hero hero--doc">
        <div class="hero__info">
            <p class="eyebrow">Curriculum Vitae</p>
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

    <div class="sheet__body">
        <h2 class="section-title">Profil</h2>
        <p class="doc__lead">{{ $c['about_bio'] }}</p>

        <h2 class="section-title doc__gap">Riwayat Pengalaman</h2>
        <ul class="timeline">
            @foreach ($experiences as $exp)
                <li>
                    <span class="timeline__period">{{ $exp->period }}</span>
                    <h3>{{ $exp->role }}</h3>
                    <span class="timeline__company">{{ $exp->company }}</span>
                    @if ($exp->description)<p>{{ $exp->description }}</p>@endif
                </li>
            @endforeach
        </ul>

        <h2 class="section-title doc__gap">Keahlian</h2>
        <div class="cols">
            @foreach ($skills->chunk(ceil(max($skills->count(), 1) / 2)) as $half)
                <div>
                    @foreach ($half as $group => $items)
                        <div class="skill-group">
                            <h4>{{ $group }}</h4>
                            @foreach ($items as $skill)
                                <div class="skill">
                                    <span>{{ $skill->name }}</span>
                                    <span class="skill__bar"><i style="width: {{ (int) $skill->level }}%"></i></span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <footer class="sheet__foot">
        <span><b>{{ $c['about_name'] }}</b> · Curriculum Vitae</span>
        <span>01 / {{ str_pad($pages, 2, '0', STR_PAD_LEFT) }}</span>
    </footer>
</section>

<section class="sheet">
    <div class="sheet__body">
        @if ($services->isNotEmpty())
            <h2 class="section-title">Bidang Layanan</h2>
            <ul class="services" style="margin-top:0">
                @foreach ($services as $svc)
                    <li><b>{{ $svc->title }}</b> — {{ $svc->description }}</li>
                @endforeach
            </ul>
        @endif

        <h2 class="section-title doc__gap">Daftar Karya</h2>
        <table class="doc__table">
            <thead>
                <tr><th>Judul</th><th>Jenis</th><th>Klien</th><th>Tahun</th><th>Teknologi</th></tr>
            </thead>
            <tbody>
                @foreach ($projects as $p)
                    <tr>
                        <td><b>{{ $p->title }}</b></td>
                        <td>{{ \App\Models\Portfolio\Project::TYPES[$p->type] ?? 'Project' }}@if ($p->category)<br><small>{{ $p->category->name }}</small>@endif</td>
                        <td>{{ $p->client ?: '—' }}</td>
                        <td>{{ $p->year ?: '—' }}</td>
                        <td><small>{{ $p->tech_list ? implode(', ', $p->tech_list) : '—' }}</small></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($stats->isNotEmpty())
            <h2 class="section-title doc__gap">Ringkasan Angka</h2>
            <ul class="stats">
                @foreach ($stats as $s)<li><b>{{ $s->value }}</b><span>{{ $s->label }}</span></li>@endforeach
            </ul>
        @endif

        @if ($testimonials->isNotEmpty())
            <h2 class="section-title doc__gap">Referensi</h2>
            <ul class="doc__quotes">
                @foreach ($testimonials as $t)
                    <li>
                        <p>“{{ $t->quote }}”</p>
                        <span><b>{{ $t->name }}</b>@if ($t->position) · {{ $t->position }}@endif</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <footer class="sheet__foot">
        <span><b>{{ $c['about_name'] }}</b> · Curriculum Vitae</span>
        <span>02 / {{ str_pad($pages, 2, '0', STR_PAD_LEFT) }}</span>
    </footer>
</section>
