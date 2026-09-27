@extends('frontend.layout')

@section('title', $content['projects_title'])
@section('meta_description', $content['projects_subtitle'] . ' — ' . $content['seo_description'])
@section('body_class', 'page-inner')

@push('jsonld')
    <script type="application/ld+json" nonce="{{ app(\App\Http\Middleware\SecurityHeaders::NONCE) }}">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $content['projects_title'],
        'url' => route('portfolio.projects'),
        'hasPart' => $projects->map(fn ($p) => ['@type' => 'CreativeWork', 'name' => $p->title, 'url' => route('portfolio.project', $p->slug)])->values(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
    </script>
@endpush

@section('content')
    <section class="section page-hero">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a> <span aria-hidden="true">/</span> <span aria-current="page">Projects</span>
            </nav>
            @include('frontend.partials.heading', ['id' => 'projects-title', 'kicker' => 'Portfolio', 'title' => $content['projects_title'], 'subtitle' => $content['projects_subtitle']])
            @if ($search !== '')
                <p class="center muted search-note">{{ $projects->count() }} hasil untuk "<b>{{ $search }}</b>" · <a href="{{ route('portfolio.projects') }}">reset</a></p>
            @endif
            @if ($projects->isEmpty())
                <p class="center muted">{{ $search !== '' ? 'Tidak ada project yang cocok.' : 'Belum ada project yang dipublikasikan.' }}</p>
            @else
                @include('frontend.partials.project-grid')
            @endif
        </div>
    </section>
@endsection
