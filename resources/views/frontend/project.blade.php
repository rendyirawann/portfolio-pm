@extends('frontend.layout')

@php
    use App\Support\Portfolio\Media;
    use Illuminate\Support\Str;
    $p = $project;
    $cover = $p->cover_image ? Media::url($p->cover_image) : $p->cover_url;

    // Paragraphs split on blank lines; a short single line without a
    // closing full stop reads as a sub-heading.
    $blocks = collect(preg_split("/\R{2,}/", trim((string) $p->description)))->filter();
@endphp

@section('title', $p->meta_title ?: $p->title)
@section('meta_description', $p->meta_description ?: ($p->summary ?: Str::limit((string) $p->description, 155)))
@section('og_image', $cover)
@section('og_type', 'article')
@section('body_class', 'page-inner')

@push('head')
    <link rel="stylesheet" href="{{ asset('assets/front/css/project-detail.css') }}?v={{ filemtime(public_path('assets/front/css/project-detail.css')) }}">
@endpush

@push('jsonld')
    <script type="application/ld+json" nonce="{{ app(\App\Http\Middleware\SecurityHeaders::NONCE) }}">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            array_filter([
                '@type' => $p->type === 'product' ? 'Product' : 'CreativeWork',
                'name' => $p->title,
                'description' => $p->summary ?: Str::limit((string) $p->description, 300),
                'image' => array_values(array_filter(array_merge([$cover], $p->images->map(fn ($i) => Media::url($i->path))->all()))),
                'url' => route('portfolio.project', $p->slug),
                'dateCreated' => $p->year ?: null,
                'keywords' => $p->tech_stack ?: null,
                'creator' => $p->type === 'product' ? null : ['@id' => url('/') . '#person'],
                'brand' => $p->type === 'product' ? ['@type' => 'Brand', 'name' => $content['brand_name']] : null,
            ]),
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Projects', 'item' => route('portfolio.projects')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $p->title, 'item' => route('portfolio.project', $p->slug)],
                ],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
    </script>
@endpush

@push('scripts')
    <script src="{{ asset('assets/front/js/project-detail.js') }}?v={{ filemtime(public_path('assets/front/js/project-detail.js')) }}" defer></script>
@endpush

@section('content')
    <article class="section page-hero detail">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a> <span aria-hidden="true">/</span>
                <a href="{{ route('portfolio.projects') }}">Projects</a> <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $p->title }}</span>
            </nav>

            <header class="detail__head" data-reveal>
                <p class="kicker">{{ \App\Models\Portfolio\Project::TYPES[$p->type] ?? 'Project' }}@if ($p->category) · {{ $p->category->name }}@endif</p>
                <h1 class="detail__title" data-pf="projects.{{ $p->id }}.title">{{ $p->title }}</h1>
                <p class="section-sub" data-pf="projects.{{ $p->id }}.summary">{{ $p->summary }}</p>
            </header>

            <figure class="detail__cover hud-corners" data-reveal>
                <img data-pf-img="projects.{{ $p->id }}.cover" src="{{ $cover }}" alt="{{ $p->title }}" width="1600" height="1000" fetchpriority="high" decoding="async">
            </figure>

            <div class="detail__grid">
                <div class="detail__content prose" data-reveal>
                    @foreach ($blocks as $block)
                        @if (! str_contains($block, "\n") && mb_strlen($block) <= 60 && ! preg_match('/[.!?:]$/u', $block))
                            <h2>{{ $block }}</h2>
                        @else
                            <p>{!! nl2br(e($block)) !!}</p>
                        @endif
                    @endforeach
                </div>

                <aside class="detail__side">
                    <div class="hud-panel" data-reveal>
                        <dl class="hud-list">
                            <div><dt>Client</dt><dd data-pf="projects.{{ $p->id }}.client">{{ $p->client }}</dd></div>
                            <div><dt>Year</dt><dd data-pf="projects.{{ $p->id }}.year">{{ $p->year }}</dd></div>
                            @if ($p->category)<div><dt>Category</dt><dd>{{ $p->category->name }}</dd></div>@endif
                        </dl>
                        @if ($p->tech_list)
                            <ul class="tags">@foreach ($p->tech_list as $tech)<li>{{ $tech }}</li>@endforeach</ul>
                        @endif
                        @if ($p->links->isNotEmpty())
                            <div class="detail__links">
                                @foreach ($p->links as $link)
                                    <a href="{{ $link->url }}" class="btn btn--glow btn--sm" target="_blank" rel="noopener nofollow">
                                        {{ $link->label }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if ($p->files->isNotEmpty())
                        <div class="hud-panel" data-reveal>
                            <h2 class="hud-panel__title">Files</h2>
                            <ul class="files">
                                @foreach ($p->files as $file)
                                    <li>
                                        <a href="{{ Media::url($file->path) }}" download="{{ $file->original_name }}" rel="nofollow">
                                            <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
                                            <span>{{ $file->original_name }}</span>
                                            <small>{{ $file->size > 1048576 ? number_format($file->size / 1048576, 1) . ' MB' : number_format($file->size / 1024) . ' KB' }}</small>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </aside>
            </div>

            @if ($p->images->isNotEmpty())
                <section class="gallery-wrap" aria-labelledby="gallery-title">
                    <h2 class="section-title section-title--sm" id="gallery-title">Gallery</h2>
                    <div class="gallery" data-gallery>
                        @foreach ($p->images as $i => $img)
                            <a href="{{ Media::url($img->path) }}" class="gallery__item" data-index="{{ $i }}" data-caption="{{ $img->caption }}" data-reveal>
                                <img src="{{ Media::url($img->thumb_path ?: $img->path) }}" alt="{{ $img->caption ?: $p->title . ' — gambar ' . ($i + 1) }}"
                                    width="{{ $img->width ?: 800 }}" height="{{ $img->height ?: 560 }}" loading="lazy" decoding="async">
                                @if ($img->caption)<span>{{ $img->caption }}</span>@endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($p->related->isNotEmpty())
                <section class="related" aria-labelledby="related-title">
                    <h2 class="section-title section-title--sm" id="related-title">More Projects</h2>
                    @include('frontend.partials.project-grid', ['projects' => $p->related, 'categories' => collect()])
                </section>
            @endif
        </div>
    </article>
@endsection
