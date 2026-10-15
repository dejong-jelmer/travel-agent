{{-- The PDF: the three parts after each other, each from a new page and with its tab marked. dompdf takes one font
    per family and weight, normal or bold, so each weight of Manrope is a family of its own. --}}
@use('App\Travel\Documents\TravelDocuments')
@php
    $medium = 'pdf';
    $tabs = [];
    foreach (array_keys(TravelDocuments::PARTS) as $part) {
        $tabs[$part] = "#{$part}";
    }
    $stayHref = fn (int $number) => "#verblijf-{$number}";
@endphp
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <title>Reisdocumenten - {{ $document->title }}</title>
    <style>
        @font-face { font-family: 'manrope'; font-weight: normal; src: url('{{ $fonts }}/Manrope-Regular.ttf') format('truetype'); }
        @font-face { font-family: 'manrope'; font-weight: bold; src: url('{{ $fonts }}/Manrope-Bold.ttf') format('truetype'); }
        @font-face { font-family: 'manrope-semibold'; src: url('{{ $fonts }}/Manrope-SemiBold.ttf') format('truetype'); }
        @font-face { font-family: 'manrope-extrabold'; src: url('{{ $fonts }}/Manrope-ExtraBold.ttf') format('truetype'); }

        @page { margin: 0; }

        @include('travel::documents.parts.styles')

        body { font-family: 'manrope'; }
        .w6 { font-family: 'manrope-semibold'; font-weight: normal; }
        .w7, strong { font-family: 'manrope'; font-weight: bold; }
        .w8 { font-family: 'manrope-extrabold'; font-weight: normal; }
        .part { page-break-before: always; }
        .part-first { page-break-before: auto; }
        {{-- Room at the top of each page: dompdf repeats the padding of the body on every page, where it cuts off that
            of any other block. The header of a part pulls itself up into it. --}}
        body { padding-top: 24px; }
        .top { margin-top: -24px; }
        {{-- dompdf sets an underline by the font height times its ratio, which puts it through the text, as it does
            the border of an inline element: the links of the PDF go without. Its own stylesheet underlines :link,
            which outweighs a plain a. --}}
        a:link { text-decoration: none; }
        {{-- dompdf puts an image with vertical-align: middle at the top of the line; 1px under the baseline its
            middle is that of the capitals. --}}
        .button img, .info-link img { vertical-align: -1px; }
    </style>
</head>

<body>
    @foreach (TravelDocuments::PARTS as $part => $label)
        <div @class(['part', 'part-first' => $loop->first]) id="{{ $part }}">
            @include('travel::documents.parts.header', ['active' => $part])
            <main class="main">
                @include("travel::documents.parts.{$part}")
            </main>
            @include('travel::documents.parts.footer')
        </div>
    @endforeach
</body>

</html>
