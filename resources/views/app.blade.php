<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicons: light mode -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/x-icon" href="/lightmode/favicon.ico" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" sizes="96x96" href="/lightmode/favicon-96x96.png" media="(prefers-color-scheme: light)">
    <!-- Favicons: dark mode -->
    <link rel="icon" type="image/svg+xml" href="/darkmode/favicon.svg" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/x-icon" href="/darkmode/favicon.ico" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="96x96" href="/darkmode/favicon-96x96.png" media="(prefers-color-scheme: dark)">
    <!-- Apple touch icon (geen media query support — altijd light) -->
    <link rel="apple-touch-icon" href="/lightmode/apple-touch-icon.png">
    <!-- Web App Manifest per kleurschema -->
    <link rel="manifest" href="/lightmode/site.webmanifest" media="(prefers-color-scheme: light)">
    <link rel="manifest" href="/darkmode/site.webmanifest" media="(prefers-color-scheme: dark)">
    <!-- Theme color voor browser UI -->
    <meta name="theme-color" content="#f5f0e8" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1e2d3d" media="(prefers-color-scheme: dark)">
    <link rel="preload" href="{{ Vite::asset('resources/fonts/poppins/Poppins-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/poppins/Poppins-SemiBold.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/images/hero-poster.webp') }}" as="image" type="image/webp">
    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body class="font-poppins">
    @inertia
</body>

</html>
