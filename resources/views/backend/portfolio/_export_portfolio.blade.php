{{-- Versi PORTFOLIO (lengkap, bergambar).
     Satu lembar profil, lalu SATU LEMBAR PER PROJECT lengkap dengan
     galeri gambarnya. Ini versi paling tebal. --}}
    {{-- ===================== PAGE 1 — PROFILE ===================== --}}
    @include('backend.portfolio._export_cover')

    {{-- ===================== PROJECT PAGES ===================== --}}
    @foreach ($projects as $i => $p)
        @php
            $cover = $p->cover_image ? \App\Support\Portfolio\Media::url($p->cover_image) : $p->cover_url;
            $thumbs = $p->images->map(fn ($img) => \App\Support\Portfolio\Media::url($img->thumb_path ?: $img->path))->reject(fn ($u) => $u === $cover);
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
                        <p class="project__desc">{!! nl2br(e($paragraphs($p->description, 0))) !!}</p>
                    </div>
                    <dl class="meta" style="margin: 0">
                        @if ($p->client)<div><dt>Klien</dt><dd>{{ $p->client }}</dd></div>@endif
                        @if ($p->year)<div><dt>Tahun</dt><dd>{{ $p->year }}</dd></div>@endif
                        @if ($p->category)<div><dt>Kategori</dt><dd>{{ $p->category->name }}</dd></div>@endif
                        @if ($p->tech_list)
                            <div><dt>Teknologi</dt><dd><ul class="tags">@foreach ($p->tech_list as $t)<li>{{ $t }}</li>@endforeach</ul></dd></div>
                        @endif
                        <div><dt>Detail</dt><dd>{{ $web }}/projects/{{ $p->slug }}</dd></div>
                        @foreach ($p->links as $link)
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

