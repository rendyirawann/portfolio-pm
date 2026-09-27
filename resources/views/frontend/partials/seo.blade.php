{{--
    SEO head for the public site. Pages may set:
      @section('title'), @section('meta_description'), @section('og_image'), @section('og_type')
      @push('jsonld') for extra structured data
--}}
@php
    use App\Support\Portfolio\Media;
    $pageTitle = trim($__env->yieldContent('title'));
    $title = $pageTitle !== '' ? $pageTitle . ' | ' . $brand['name'] : $content['seo_title'];
    $description = \Illuminate\Support\Str::limit(trim($__env->yieldContent('meta_description')) ?: (string) $content['seo_description'], 160, '…');
    $ogImage = trim($__env->yieldContent('og_image'))
        ?: Media::url($content['seo_og_image'] ?: ($content['hero_image'] ?: null), 'assets/media/branding/og-image.png');
    $canonical = url()->current();
    $nonce = app(\App\Http\Middleware\SecurityHeaders::NONCE);
    $sameAs = $socials->pluck('url')->filter(fn ($u) => str_starts_with($u, 'http'))->values();
@endphp
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $content['seo_keywords'] }}">
<meta name="author" content="{{ $brand['author'] }}">
<meta name="robots" content="{{ $content['seo_robots'] ?: 'index, follow' }}, max-image-preview:large">
<meta name="theme-color" content="{{ $content['seo_theme_color'] ?: '#07070d' }}">
<meta name="color-scheme" content="dark">
<meta name="format-detection" content="telephone=no">
<link rel="canonical" href="{{ $canonical }}">
@if ($content['seo_google_verification'])
    <meta name="google-site-verification" content="{{ $content['seo_google_verification'] }}">
@endif

<meta property="og:type" content="{{ trim($__env->yieldContent('og_type')) ?: 'website' }}">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="{{ $brand['name'] }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:alt" content="{{ $title }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<link rel="icon" href="{{ $brand['favicon_url'] }}" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="{{ $brand['favicon_png_url'] }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ $brand['apple_icon_url'] }}">
<link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

<script type="application/ld+json" nonce="{{ $nonce }}">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => $brand['name'],
            'description' => $content['seo_description'],
            'inLanguage' => 'id-ID',
        ],
        array_filter([
            '@type' => 'Person',
            '@id' => url('/') . '#person',
            'name' => $brand['author'],
            'jobTitle' => $content['about_role'],
            'description' => $content['about_bio'],
            'url' => url('/'),
            'image' => $content['about_photo'] ? Media::url($content['about_photo']) : null,
            'email' => $content['contact_email'] ? 'mailto:' . $content['contact_email'] : null,
            'address' => $content['about_location'] ? ['@type' => 'PostalAddress', 'addressLocality' => $content['about_location']] : null,
            'sameAs' => $sameAs->all() ?: null,
        ]),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
</script>
@stack('jsonld')
