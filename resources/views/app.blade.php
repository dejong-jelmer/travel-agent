<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/lightmode/favicon.ico">
    <link rel="icon" type="image/png" sizes="96x96" href="/lightmode/favicon-96x96.png">
    <link rel="apple-touch-icon" href="/lightmode/apple-touch-icon.png">
    <link rel="manifest" href="/lightmode/site.webmanifest">
    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body class="font-poppins">
    @inertia
</body>

</html>
