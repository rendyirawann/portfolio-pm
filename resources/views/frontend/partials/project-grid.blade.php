@php $active = $activeCategory ?? null; @endphp
@if ($categories->count() > 1)
    <div class="filters" role="tablist" aria-label="Filter kategori" data-filters>
        <button type="button" class="chip {{ $active ? '' : 'is-active' }}" data-filter="*" role="tab" aria-selected="{{ $active ? 'false' : 'true' }}">Semua</button>
        @foreach ($categories as $cat)
            <button type="button" class="chip {{ $active === $cat->slug ? 'is-active' : '' }}" data-filter="{{ $cat->slug }}" role="tab"
                aria-selected="{{ $active === $cat->slug ? 'true' : 'false' }}">{{ $cat->name }}</button>
        @endforeach
    </div>
@endif

<div class="projects" data-projects>
    @foreach ($projects as $i => $p)
        <article class="project" data-category="{{ $p->category?->slug }}" @if ($active && $p->category?->slug !== $active) hidden @endif data-reveal>
            <a href="{{ route('portfolio.project', $p->slug) }}" class="project__link" aria-label="{{ $p->title }}">
                <div class="project__media">
                    <img data-pf-img="projects.{{ $p->id }}.cover" src="{{ $p->cover_url }}" alt="{{ $p->title }}" width="800" height="600" loading="lazy" decoding="async">
                    <span class="project__type">{{ \App\Models\Portfolio\Project::TYPES[$p->type] ?? 'Project' }}</span>
                    @if ($p->is_featured)<span class="project__featured">Featured</span>@endif
                </div>
                <div class="project__body">
                    <span class="project__index" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="project__title" data-pf="projects.{{ $p->id }}.title">{{ $p->title }}</h3>
                    <p class="project__meta">{{ $p->category?->name }}@if ($p->year) · {{ $p->year }}@endif</p>
                    <p class="project__summary" data-pf="projects.{{ $p->id }}.summary">{{ $p->summary }}</p>
                    @if ($p->tech_list)
                        <ul class="tags">
                            @foreach (array_slice($p->tech_list, 0, 4) as $tech)<li>{{ $tech }}</li>@endforeach
                        </ul>
                    @endif
                    <span class="project__more" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i><span>Lihat detail</span></span>
                </div>
            </a>
        </article>
    @endforeach
</div>
