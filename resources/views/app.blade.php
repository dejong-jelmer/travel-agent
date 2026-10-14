<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth" data-reveal="{{ config('app.reveal_animations') ? 'on' : 'off' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicons: light mode -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/x-icon" href="/lightmode/favicon.ico" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" sizes="96x96" href="/lightmode/favicon-96x96.png"
        media="(prefers-color-scheme: light)">
    <!-- Favicons: dark mode -->
    <link rel="icon" type="image/svg+xml" href="/darkmode/favicon.svg" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/x-icon" href="/darkmode/favicon.ico" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="96x96" href="/darkmode/favicon-96x96.png"
        media="(prefers-color-scheme: dark)">
    <!-- Apple touch icon (geen media query support - altijd light) -->
    <link rel="apple-touch-icon" href="/lightmode/apple-touch-icon.png">
    <!-- Web App Manifest per kleurschema -->
    <link rel="manifest" href="/lightmode/site.webmanifest" media="(prefers-color-scheme: light)">
    <link rel="manifest" href="/darkmode/site.webmanifest" media="(prefers-color-scheme: dark)">
    <!-- Theme color voor browser UI -->
    <meta name="theme-color" content="#f5f0e8" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1e2d3d" media="(prefers-color-scheme: dark)">
    {{-- The weights above the fold: bold for the hero titles, regular for the text. Other weights load on use. --}}
    <link rel="preload" href="{{ Vite::asset('resources/fonts/poppins/Poppins-Regular.woff2') }}" as="font"
        type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/poppins/Poppins-Bold.woff2') }}" as="font"
        type="font/woff2" crossorigin>
    {{-- The hero image of the page, set through its seo prop (HasPageMetadata::preloadImageSeo); one per media query
         when the page shows a different image per screen --}}
    @foreach ($page['props']['seo']['preload_images'] ?? [] as $preloadImage)
        <link rel="preload" as="image" href="{{ $preloadImage['href'] }}" @if ($preloadImage['srcset']) imagesrcset="{{ $preloadImage['srcset'] }}" imagesizes="{{ $preloadImage['sizes'] }}" @endif @if ($preloadImage['media']) media="{{ $preloadImage['media'] }}" @endif fetchpriority="high">
    @endforeach
    <style>
        .js-cookie-consent {
            opacity: 0;
        }
    </style>
    @if (app()->env === 'production')
        <script defer src="https://stats.omdatwereizen.nl/script.js" data-website-id="654796c2-5542-4877-aa8e-d7660e1e1d2b">
        </script>
    @endif
    @include('seo')
    {{-- Public pages leave the admin routes out (config/ziggy.php) --}}
    @routes(request()->is('admin*') ? null : 'public')
    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body class="font-poppins">
    @inertia
    @if (app()->env === 'production')
        @include('cookie-consent::index')
    @endif
</body>

</html>
