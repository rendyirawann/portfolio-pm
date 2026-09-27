{{-- Versi RESUME — dua kolom, TANPA foto (sesuai konvensi resume).
     Ringkas dan selektif: hanya intisari, dirancang muat satu halaman.
     Riwayat selengkapnya ada di versi CV. --}}

@php
    // Pembatasan di sini disengaja — inilah yang membedakan resume dari CV.
    $expTop = $experiences->take(3);
    $skillTop = $skills->map(fn ($items) => $items->sortByDesc('level')->take(5));
    $projTop = $projects->sortByDesc('is_featured')->take(4);
    $bio = \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', (string) $c['about_bio']), 340);
@endphp

<section class="sheet sheet--flow">
    <div class="doc2">
        <header class="doc2__head doc2__head--nophoto">
            <div>
                <p class="doc2__kicker">Resume</p>
                <h1 class="doc2__name">{{ $c['about_name'] }}</h1>
                <p class="doc2__role">{{ $c['about_role'] }}</p>
                <div class="doc2__line">
                    @if ($c['contact_email'])<span><em>Email</em>{{ $c['contact_email'] }}</span>@endif
                    @if ($wa)<span><em>WhatsApp</em>+{{ $wa }}</span>@endif
                    @if ($c['about_location'])<span><em>Lokasi</em>{{ $c['about_location'] }}</span>@endif
                    <span><em>Web</em>{{ $web }}</span>
                </div>
            </div>
        </header>

        <div class="doc2__body">
            <aside>
                <div class="doc2__sec">
                    <h2 class="doc2__h">Keahlian Inti</h2>
                    @foreach ($skillTop as $group => $items)
                        <div class="doc2__skill">
                            <h5>{{ $group }}</h5>
                            <ul class="doc2__tags">
                                @foreach ($items as $skill)<li>{{ $skill->name }}</li>@endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

                @if ($stats->isNotEmpty())
                    <div class="doc2__sec">
                        <h2 class="doc2__h">Sorotan</h2>
                        <dl class="doc2__meta">
                            @foreach ($stats->take(4) as $s)
                                <div><dt>{{ $s->label }}</dt><dd>{{ $s->value }}</dd></div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </aside>

            <main>
                <div class="doc2__sec">
                    <h2 class="doc2__h">Ringkasan</h2>
                    <p class="doc2__lead">{{ $bio }}</p>
                </div>

                <div class="doc2__sec">
                    <h2 class="doc2__h">Pengalaman Terbaru</h2>
                    <ul class="doc2__exp">
                        @foreach ($expTop as $exp)
                            <li>
                                <span class="doc2__when">{{ $exp->period }}</span>
                                <h3 class="doc2__what">{{ $exp->role }}</h3>
                                <p class="doc2__where">{{ $exp->company }}</p>
                                @if ($exp->description)
                                    <p>{{ \Illuminate\Support\Str::limit($exp->description, 170) }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($projTop->isNotEmpty())
                    <div class="doc2__sec">
                        <h2 class="doc2__h">Karya Unggulan</h2>
                        <ul class="doc2__works">
                            @foreach ($projTop as $p)
                                <li>
                                    <b>{{ $p->title }}</b>
                                    <span class="doc2__when">{{ $p->year ?: '—' }}</span>
                                    @if ($p->summary)<p>{{ \Illuminate\Support\Str::limit($p->summary, 120) }}</p>@endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </main>
        </div>

        <p class="doc2__note">
            Riwayat lengkap, seluruh keahlian, dan daftar karya menyeluruh tersedia pada versi <b>CV</b> dan <b>Portfolio</b>.
        </p>

        <footer class="sheet__foot">
            <span><b>{{ $c['about_name'] }}</b> · Resume</span>
            <span>{{ now()->translatedFormat('F Y') }}</span>
        </footer>
    </div>
</section>
