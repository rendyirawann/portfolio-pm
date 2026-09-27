{{-- Versi CV (Curriculum Vitae) — tata letak dua kolom.
     Sidebar untuk data ringkas (kontak, keahlian, layanan), kolom utama untuk
     narasi (profil, riwayat, karya, referensi). Isinya LENGKAP dan tidak
     dipangkas — itu yang membedakannya dari resume. Memakai foto profil. --}}

<section class="sheet sheet--flow">
    <div class="doc2">
        <header class="doc2__head">
            <div class="doc2__photo"><img src="{{ $photo }}" alt="{{ $c['about_name'] }}"></div>
            <div>
                <p class="doc2__kicker">Curriculum Vitae</p>
                <h1 class="doc2__name">{{ $c['about_name'] }}</h1>
                <p class="doc2__role">{{ $c['about_role'] }}</p>
                <div class="doc2__line">
                    @if ($c['contact_email'])<span><em>Email</em>{{ $c['contact_email'] }}</span>@endif
                    @if ($wa)<span><em>WhatsApp</em>+{{ $wa }}</span>@endif
                    @if ($c['about_location'])<span><em>Lokasi</em>{{ $c['about_location'] }}</span>@endif
                    <span><em>Web</em>{{ $web }}</span>
                    @if ($linkedin)<span><em>LinkedIn</em>{{ $linkedin }}</span>@endif
                </div>
            </div>
        </header>

        <div class="doc2__body">
            {{-- ---------- Sidebar ---------- --}}
            <aside>
                <div class="doc2__sec">
                    <h2 class="doc2__h">Keahlian</h2>
                    @foreach ($skills as $group => $items)
                        <div class="doc2__skill">
                            <h5>{{ $group }}</h5>
                            <ul>
                                @foreach ($items as $skill)
                                    <li>
                                        {{ $skill->name }}
                                        <span><i style="width: {{ (int) $skill->level }}%"></i></span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

                @if ($services->isNotEmpty())
                    <div class="doc2__sec">
                        <h2 class="doc2__h">Bidang Layanan</h2>
                        <ul class="doc2__tags">
                            @foreach ($services as $svc)<li>{{ $svc->title }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                @if ($stats->isNotEmpty())
                    <div class="doc2__sec">
                        <h2 class="doc2__h">Ringkasan Angka</h2>
                        <dl class="doc2__meta">
                            @foreach ($stats as $s)
                                <div><dt>{{ $s->label }}</dt><dd>{{ $s->value }}</dd></div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </aside>

            {{-- ---------- Kolom utama ---------- --}}
            <main>
                <div class="doc2__sec">
                    <h2 class="doc2__h">Profil</h2>
                    <p class="doc2__lead">{{ $c['about_bio'] }}</p>
                </div>

                <div class="doc2__sec">
                    <h2 class="doc2__h">Riwayat Pengalaman</h2>
                    <ul class="doc2__exp">
                        @foreach ($experiences as $exp)
                            <li>
                                <span class="doc2__when">{{ $exp->period }}</span>
                                <h3 class="doc2__what">{{ $exp->role }}</h3>
                                <p class="doc2__where">{{ $exp->company }}</p>
                                @if ($exp->description)<p>{{ $exp->description }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="doc2__sec">
                    <h2 class="doc2__h">Daftar Karya</h2>
                    <ul class="doc2__works">
                        @foreach ($projects as $p)
                            <li>
                                <b>{{ $p->title }}</b>
                                <span class="doc2__when">{{ $p->year ?: '—' }}</span>
                                @if ($p->summary)<p>{{ $p->summary }}</p>@endif
                                <small>
                                    {{ \App\Models\Portfolio\Project::TYPES[$p->type] ?? 'Project' }}@if ($p->category) · {{ $p->category->name }}@endif
                                    @if ($p->client) · Klien: {{ $p->client }}@endif
                                    @if ($p->tech_list) · {{ implode(', ', $p->tech_list) }}@endif
                                </small>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($testimonials->isNotEmpty())
                    <div class="doc2__sec">
                        <h2 class="doc2__h">Referensi</h2>
                        <ul class="doc2__quote">
                            @foreach ($testimonials as $t)
                                <li>
                                    <p>“{{ $t->quote }}”</p>
                                    <span><b>{{ $t->name }}</b>@if ($t->position) · {{ $t->position }}@endif</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </main>
        </div>

        <footer class="sheet__foot">
            <span><b>{{ $c['about_name'] }}</b> · Curriculum Vitae</span>
            <span>{{ now()->translatedFormat('F Y') }}</span>
        </footer>
    </div>
</section>
