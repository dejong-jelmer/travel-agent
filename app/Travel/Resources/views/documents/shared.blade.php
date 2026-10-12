<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Reisdocumenten - {{ $document->title }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #1e2d3d;
            background-color: #ffffff;
        }

        .wrap {
            max-width: 760px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .top {
            background-color: #2d5f6e;
            color: #ffffff;
            padding: 28px 0 24px;
        }

        .kicker {
            margin: 0;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #a3bccb;
        }

        h1 {
            margin: 4px 0;
            font-size: 28px;
            line-height: 1.2;
        }

        .top p {
            margin: 0;
        }

        .download {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 18px;
            border-radius: 12px;
            background-color: #f59e0b;
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
        }

        .download:hover,
        .download:focus-visible {
            background-color: #ffffff;
            color: #2d5f6e;
        }

        .part {
            margin: 32px 0;
        }

        h2 {
            margin: 0 0 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #afcb98;
            font-size: 22px;
            color: #2d5f6e;
        }

        h3 {
            margin: 20px 0 6px;
            font-size: 18px;
        }

        h4 {
            margin: 14px 0 4px;
            font-size: 16px;
        }

        p {
            margin: 0 0 6px;
        }

        .muted {
            color: #6b7280;
            font-weight: normal;
        }

        .note {
            font-style: italic;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.legs td {
            padding: 8px 4px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .legs-time,
        .legs-duration {
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .legs-time {
            padding-right: 12px !important;
        }

        .legs-duration {
            text-align: right;
            color: #6b7280;
        }

        .dot {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .stay {
            margin: 16px 0;
            padding: 10px 14px;
            border-radius: 8px;
            background-color: #f5f0e8;
        }

        .accommodation {
            margin-bottom: 28px;
        }

        table.facts th {
            width: 34%;
            padding: 3px 12px 3px 0;
            text-align: left;
            vertical-align: top;
            font-weight: normal;
            color: #6b7280;
        }

        table.facts td {
            padding: 3px 0;
        }

        .walk {
            margin-top: 8px;
        }

        .city {
            margin-bottom: 12px;
        }

        footer {
            padding: 24px 16px 40px;
            font-size: 14px;
            color: #6b7280;
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 24px;
            }

            .legs-time {
                padding-right: 8px !important;
            }

            .legs-time span {
                display: block;
            }

            .legs-time .legs-to {
                display: none;
            }

            table.facts th {
                width: 40%;
            }
        }
    </style>
</head>

<body>
    <header class="top">
        <div class="wrap">
            <p class="kicker">Reisdocumenten</p>
            <h1>{{ $document->title }}</h1>
            @if ($document->travelerNames !== [])
                <p>{{ implode(', ', $document->travelerNames) }}</p>
            @endif
            <a class="download" href="{{ $pdfUrl }}">Download als PDF</a>
        </div>
    </header>

    <main class="wrap">
        @include('travel::documents.parts.schedule')
        @include('travel::documents.parts.accommodations')
        @include('travel::documents.parts.practical')
    </main>

    <footer class="wrap">{{ config('app.name') }}</footer>
</body>

</html>
