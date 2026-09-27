{{-- Versi PORTFOLIO RINGKAS.
     Lembar profil yang sama, tetapi project & produk disajikan sebagai DAFTAR
     + DETAIL TEKS saja — tanpa gambar sampul maupun galeri. Cocok dikirim
     lewat email karena ukurannya jauh lebih kecil. --}}

@include('backend.portfolio._export_cover', ['eyebrow' => 'Portfolio ringkas ' . now()->year])

@php
    // 4 project per lembar supaya tiap entri masih lega dibaca.
    $chunks = $projects->chunk(4);
@endphp

@foreach ($chunks as $ci => $chunk)
    <section class="sheet sheet--flow">
        <div class="sheet__body">
            @if ($ci === 0)
                <h2 class="section-title">Project &amp; Produk</h2>
                <p class="lite__lead">{{ $projects->count() }} karya terpilih — ringkasan, detail, teknologi, dan tautannya.</p>
            @else
                <h2 class="section-title">Project &amp; Produk <span class="lite__cont">(lanjutan)</span></h2>
            @endif

            <ol class="lite" start="{{ $ci * 4 + 1 }}">
                @foreach ($chunk as $p)
                    <li class="lite__item">
                        <div class="lite__head">
                            <h3 class="lite__title">{{ $p->title }}</h3>
                            <span class="lite__type">
                                {{ \App\Models\Portfolio\Project::TYPES[$p->type] ?? 'Project' }}@if ($p->category) · {{ $p->category->name }}@endif
                            </span>
                        </div>

                        @if ($p->summary)<p class="lite__summary">{{ $p->summary }}</p>@endif
                        @if ($p->description)<p class="lite__desc">{!! nl2br(e($paragraphs($p->description, 0))) !!}</p>@endif

                        <dl class="lite__meta">
                            @if ($p->client)<div><dt>Klien</dt><dd>{{ $p->client }}</dd></div>@endif
                            @if ($p->year)<div><dt>Tahun</dt><dd>{{ $p->year }}</dd></div>@endif
                            @if ($p->tech_list)
                                <div><dt>Teknologi</dt><dd><ul class="tags">@foreach ($p->tech_list as $t)<li>{{ $t }}</li>@endforeach</ul></dd></div>
                            @endif
                            @if ($p->links->isNotEmpty())
                                <div><dt>Tautan</dt><dd>{{ $p->links->map(fn ($l) => $l->label . ': ' . preg_replace('#^https?://(www\.)?#', '', $l->url))->implode(' · ') }}</dd></div>
                            @endif
                        </dl>
                    </li>
                @endforeach
            </ol>
        </div>

        <footer class="sheet__foot">
            <span><b>{{ $c['about_name'] }}</b> · Project &amp; Produk</span>
            <span>{{ str_pad($ci + 2, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($pages, 2, '0', STR_PAD_LEFT) }}</span>
        </footer>
    </section>
@endforeach
