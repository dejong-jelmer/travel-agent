{{-- The Reisschema part: the journeys and the stays in the order of the trip, the tickets after the first journey,
    and what to do on a delay. --}}
@use('App\Travel\Data\JourneyData')
@use('App\Travel\Documents\Format')
@use('App\Travel\Documents\Icon')
@php
    $ticketsTold = false;
@endphp
@forelse ($document->schedule() as $entry)
    <div @class(['block', 'block-first' => $loop->first])>
        @if ($entry instanceof JourneyData)
            @include('travel::documents.parts.journey', ['journey' => $entry])
        @else
            @include('travel::documents.parts.stay', [
                'stay' => $entry,
                'stayNumber' => array_search($entry, $document->stays, true) + 1,
            ])
        @endif
    </div>
    @if ($entry instanceof JourneyData && ! $ticketsTold)
        @php
            $ticketsTold = true;
        @endphp
        <div class="block tip">
            <table class="full">
                <tr class="top-align">
                    <td style="width: 20px"><img class="icon-20" src="{{ Icon::src('ticket', '#7a4a00') }}" alt=""></td>
                    <td class="text">
                        Je tickets ontvang je als pdf per e-mail. Bewaar ze offline op je telefoon, zodat je er ook zonder
                        bereik bij kunt, en neem een geprinte set mee als reserve.
                    </td>
                </tr>
            </table>
        </div>
    @endif
@empty
    <p class="muted">Het reisschema volgt nog.</p>
@endforelse

@if ($document->journeys !== [])
    <section class="block panel">
        <h2 class="w8">Vertraging? Zo los je het op</h2>
        <p class="panel-intro">Mis je een aansluiting, dan is er bijna altijd een oplossing. Deze stappen helpen je verder.</p>
        @php
            $steps = [
                'Spreek het treinpersoneel aan. Zij kennen de alternatieven en helpen je verder.',
                'Vraag op een groter station bij de servicebalie om een <strong>aantekening van de vertraging</strong>. Daarmee mag je mee met de volgende trein van dezelfde vervoerder, ook bij de TGV. Een zitplaats is dan niet zeker, maar je komt vooruit.',
                ...($document->usesLine('Eurostar') ? ['Mis je de Eurostar? Ga naar de Eurostar-balie, daar wisselen ze je ticket om.'] : []),
                'Moet je een nieuw ticket kopen? Overleg eerst met mij. Moet het toch ter plekke, bewaar dan je ticket en de aantekening. De vergoeding regel ik achteraf.',
            ];
        @endphp
        <table class="steps full">
            @foreach ($steps as $step)
                <tr class="top-align">
                    <td class="number"><div class="number-circle w8">{{ $loop->iteration }}</div></td>
                    <td class="step">{!! $step !!}</td>
                </tr>
            @endforeach
        </table>
        <div class="call">
            Kom je er niet uit? Bel me. Ik zoek je verdere reis uit en laat je accommodatie weten dat je later aankomt.
            @if ($document->emergencyPhone)
                <a class="button w8" href="{{ Format::phoneUrl($document->emergencyPhone) }}">
                    <img src="{{ Icon::src('phone', '#ffffff') }}" alt="">Bel {{ config('app.name') }}
                </a>
            @endif
        </div>
    </section>
@endif
