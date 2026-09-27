{{-- Lembar profil (halaman 1) — dipakai versi PORTFOLIO dan PORTFOLIO RINGKAS.
     Variabel opsional: $eyebrow (teks kecil di atas nama). --}}
@php $eyebrow = $eyebrow ?? ('Portfolio ' . now()->year); @endphp
    <section class="sheet">
        <header class="hero">
            <div class="hero__photo"><img src="{{ $photo }}" alt="{{ $c['about_name'] }}"></div>
            <div class="hero__info">
                <p class="eyebrow">{{ $eyebrow }}</p>
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
                    @foreach ($stats as $s)
                        <li><b>{{ $s->value }}</b><span>{{ $s->label }}</span></li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="cols">
            <div>
                <h2 class="section-title">Keahlian</h2>
                @foreach ($skills as $group => $items)
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

            <div>
                <h2 class="section-title">Pengalaman</h2>
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

                @if ($services->isNotEmpty())
                    <h2 class="section-title" style="margin-top: 8mm">Layanan</h2>
                    <ul class="services" style="margin-top: 0">
                        @foreach ($services as $svc)
                            <li><b>{{ $svc->title }}</b> — {{ $svc->description }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <footer class="sheet__foot"><span><b>{{ $c['about_name'] }}</b> · {{ $c['about_role'] }}</span><span>01 / {{ str_pad($pages, 2, '0', STR_PAD_LEFT) }}</span></footer>
    </section>
