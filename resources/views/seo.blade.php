@php
    // SEO data travels as the page's `seo` Inertia prop (see HasPageMetadata::shareSeo).
    // Reading it here keeps the head tags in sync without mutating global View state.
    // Pages without it (login, thank-you and admin pages) are kept out of search results.
    $seo = $page['props']['seo'] ?? [];
    $jsonLd = $seo['jsonLd'] ?? null;

    $appUrl = rtrim(config('app.url'), '/');
    $canonical = $appUrl . '/' . ltrim(request()->path() === '/' ? '' : request()->path(), '/');

    // Query strings (utm, ref) never create a separate URL, except the page of a paginated listing.
    $pageNumber = request()->integer('page');
    if ($pageNumber > 1) {
        $canonical .= '?page=' . $pageNumber;
    }

    $seoTitle = $seo['title'] ?? config('app.name');
    $seoDescription = $seo['description'] ?? '';
    $seoType = $seo['og_type'] ?? 'website';
    $seoRobots = $seo['robots'] ?? 'noindex, follow';
    $ogLocale = config('seo.og_locales.' . app()->getLocale());
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="{{ $seoType }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
@if ($ogLocale)
    <meta property="og:locale" content="{{ $ogLocale }}">
@endif
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
@isset($seo['og_image'])
    <meta property="og:image" content="{{ $seo['og_image'] }}">
    <meta property="og:image:type" content="{{ $seo['og_image_type'] }}">
    <meta property="og:image:width" content="{{ $seo['og_image_width'] }}">
    <meta property="og:image:height" content="{{ $seo['og_image_height'] }}">
    <meta property="og:image:alt" content="{{ $seo['og_image_alt'] }}">
@endisset
@isset($seo['article_published_time'])
    <meta property="article:published_time" content="{{ $seo['article_published_time'] }}">
@endisset
@isset($seo['article_modified_time'])
    <meta property="article:modified_time" content="{{ $seo['article_modified_time'] }}">
@endisset

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
@isset($seo['og_image'])
    <meta name="twitter:image" content="{{ $seo['og_image'] }}">
    <meta name="twitter:image:alt" content="{{ $seo['og_image_alt'] }}">
@endisset

@if(!empty($jsonLd))
    <script type="application/ld+json">
        {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endif
