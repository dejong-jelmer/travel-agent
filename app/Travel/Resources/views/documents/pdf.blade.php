<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <title>Reisdocumenten - {{ $document->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 18mm 16mm 20mm 16mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            line-height: 1.45;
            color: #1e2d3d;
        }

        .header {
            background-color: #2d5f6e;
            color: #ffffff;
            margin: -18mm -16mm 18px -16mm;
            padding: 26px 16mm 18px 16mm;
        }

        .header .kicker {
            font-size: 8pt;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #a3bccb;
            margin: 0;
        }

        .header h1 {
            font-size: 20pt;
            margin: 2px 0 4px 0;
        }

        .header p {
            font-size: 9pt;
            margin: 0;
        }

        .part + .part {
            page-break-before: always;
        }

        h2 {
            font-size: 14pt;
            color: #2d5f6e;
            border-bottom: 2px solid #afcb98;
            padding-bottom: 3px;
            margin: 0 0 8px 0;
        }

        h3 {
            font-size: 11pt;
            margin: 14px 0 4px 0;
        }

        h4 {
            font-size: 9.5pt;
            margin: 10px 0 2px 0;
        }

        p {
            margin: 0 0 4px 0;
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

        tr {
            page-break-inside: avoid;
        }

        table.legs td {
            padding: 5px 4px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .legs-time {
            width: 23%;
            white-space: nowrap;
        }

        .legs-duration {
            width: 13%;
            text-align: right;
            white-space: nowrap;
            color: #6b7280;
        }

        .dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 4px;
        }

        .stay {
            background-color: #f5f0e8;
            padding: 7px 10px;
            margin: 12px 0;
        }

        .accommodation {
            margin-bottom: 18px;
        }

        table.facts th {
            text-align: left;
            font-weight: normal;
            color: #6b7280;
            width: 26%;
            padding: 2px 0;
            vertical-align: top;
        }

        table.facts td {
            padding: 2px 0;
        }

        .walk {
            margin-top: 6px;
        }

        .city {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <p class="kicker">Reisdocumenten</p>
        <h1>{{ $document->title }}</h1>
        @if ($document->travelerNames !== [])
            <p>{{ implode(', ', $document->travelerNames) }}</p>
        @endif
    </div>

    @include('travel::documents.parts.schedule')
    @include('travel::documents.parts.accommodations')
    @include('travel::documents.parts.practical')
</body>

</html>
