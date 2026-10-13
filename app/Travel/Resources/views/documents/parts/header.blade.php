{{-- The header of a part: the logo, the period and title of the trip, and the tabs, with $active the current part. --}}
@use('App\Travel\Documents\Format')
@use('App\Travel\Documents\Icon')
@use('App\Travel\Documents\TravelDocuments')
<header class="top">
    <table class="full">
        <tr class="top-align">
            <td>
                <img class="logo" src="{{ Icon::logo() }}" alt="{{ config('app.name') }}">
            </td>
            <td class="right">
                <span class="kicker w7">Jouw reis</span>
            </td>
        </tr>
    </table>

    @if ($period = Format::period($document->firstDay(), $document->lastDay()))
        <p class="period w6">{{ $period }}@if ($document->nights() > 0) · {{ Format::nights($document->nights()) }}@endif</p>
    @endif
    <h1 class="w8">{{ $document->title }}</h1>
    @if ($document->travelerNames !== [])
        <p class="travelers">Voor {{ Format::list($document->travelerNames) }}</p>
    @endif

    <nav class="tabs" aria-label="Reisdocumenten">
        <table class="full">
            <tr>
                @foreach (TravelDocuments::PARTS as $part => $label)
                    <td>
                        <a href="{{ $tabs[$part] }}" @class(['tab', 'tab-active w7' => $part === $active, 'w6' => $part !== $active])
                            @if ($part === $active) aria-current="page" @endif>{{ $label }}</a>
                    </td>
                @endforeach
            </tr>
        </table>
    </nav>
    @if ($medium === 'web')
        <a class="download w7" href="{{ $pdfUrl }}">Download als pdf</a>
    @endif
</header>
