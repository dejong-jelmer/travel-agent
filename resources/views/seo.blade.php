@php
    $appUrl = rtrim(config('app.url'), '/');
    $canonical = $appUrl . '/' . ltrim(request()->path() === '/' ? '' : request()->path(), '/');

    $seoTitle = $seo['title'] ?? config('app.name');
    $seoDescription = $seo['description'] ?? '';
    $seoImage = $seo['og_image'] ?? asset(config('seo.default_og_image'));
    $seoType = $seo['og_type'] ?? 'website';
    $seoRobots = $seo['robots'] ?? 'index, follow';

    if (! str_starts_with($seoImage, 'http')) {
        $seoImage = $appUrl . '/' . ltrim($seoImage, '/');
    }
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="{{ $seoType }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="nl_NL">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $seo['og_image_alt'] ?? $seoTitle }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

@if(!empty($jsonLd))
    <script type="application/ld+json">
        {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endif
