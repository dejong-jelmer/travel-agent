{{-- The page behind the share link: one part at a time, $active, with tabs to the others and the PDF to keep. In a
    column as wide as a phone, also on a larger screen. --}}
@use('App\Travel\Documents\TravelDocuments')
@php($medium = 'web')
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ TravelDocuments::PARTS[$active] }} - {{ $document->title }}</title>
    <style>
        @font-face {
            font-family: 'Manrope';
            font-style: normal;
            font-weight: 400 800;
            font-display: swap;
            src: url('{{ route('travel.documents.font', 'manrope-latin-ext.woff2') }}') format('woff2');
            unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF;
        }

        @font-face {
            font-family: 'Manrope';
            font-style: normal;
            font-weight: 400 800;
            font-display: swap;
            src: url('{{ route('travel.documents.font', 'manrope-latin.woff2') }}') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }

        @include('travel::documents.parts.styles')

        body { font-family: 'Manrope', system-ui, -apple-system, 'Segoe UI', sans-serif; -webkit-text-size-adjust: 100%; }
        * { box-sizing: border-box; }
        .page { display: flex; flex-direction: column; min-height: 100vh; max-width: 480px; margin: 0 auto; background: #faf7f2; }
        .page .main { flex: 1; }
        .tab { min-height: 44px; }
        .tab:not(.tab-active):hover { background: #e9e1d4; }
        {{-- A link keeps its color on hover, as links on a dark ground are white: an underline thickens, or shows. --}}
        a:hover { text-decoration-thickness: 2px; }
        .route-to:hover { text-decoration: underline; }
        .foot-row:hover, .button:hover { opacity: 0.92; }
        .download { display: block; margin-top: 14px; font-size: 13px; text-align: center; }
        .checklist label { cursor: pointer; }

        @media (min-width: 520px) {
            html, body { background: #ebe4d8; }
            .page { border-left: 1px solid #e6dfd4; border-right: 1px solid #e6dfd4; }
        }
    </style>
</head>

<body>
    <div class="page">
        @include('travel::documents.parts.header')
        <main class="main">
            @include("travel::documents.parts.{$active}")
        </main>
        @include('travel::documents.parts.footer')
    </div>
</body>

</html>
