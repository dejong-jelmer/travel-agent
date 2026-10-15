{{-- A leg of a journey. A transfer is a row between the cards; a ride with a timetable a card with its stops; a leg
    without one, such as the metro across Paris, a hop that belongs to the change. $final marks the end of the day. --}}
@use('App\Travel\Documents\Format')
@use('App\Travel\Documents\Icon')
@use('App\Travel\Enums\LegType')
@php
    // The lines with a page elsewhere about them, each linked under the ride.
    $infoLines = array_filter($leg->transitLines, fn ($line) => $line->infoLink !== null);
@endphp
@if ($leg->type === LegType::Transfer)
    <div class="change">
        <table class="full">
            <tr>
                <td style="width: 16px"><img class="icon" src="{{ Icon::src('transfer', Icon::MUTED) }}" alt=""></td>
                <td class="text">Overstappen in <strong class="ink w7">{{ $leg->fromStation }}</strong></td>
                @if ($leg->durationMinutes !== null)
                    <td class="right nowrap">
                        @if ($leg->durationMinutes <= Format::SHORT_TRANSFER_MINUTES)
                            <span class="short w7">{{ Format::duration($leg->durationMinutes) }} · kort</span>
                        @else
                            <span class="ink w7">{{ Format::duration($leg->durationMinutes) }}</span>
                        @endif
                    </td>
                @endif
            </tr>
        </table>
    </div>
@elseif ($leg->departureAt !== null)
    @php
        // The name of the line, and after it the train number or else the direction: 'trein 9416', 'richting Den Helder'.
        $lines = array_map(fn ($line) => $line->name, $leg->transitLines);
        $direction = ($leg->transitLines[0] ?? null)?->direction;
        $detail = match (true) {
            $leg->number !== null => mb_strtolower($leg->type->label()).' '.$leg->number,
            $direction !== null => 'richting '.$direction,
            default => null,
        };
    @endphp
    <div class="card">
        <table class="full">
            <tr>
                <td class="card-icon"><img class="icon" src="{{ Icon::src('train') }}" alt=""></td>
                <td class="card-line">
                    <span class="w7">{{ $lines === [] ? $leg->type->label() : implode(' / ', $lines) }}</span>
                    @if ($detail)<span class="muted">{{ $detail }}</span>@endif
                </td>
                <td class="card-duration w6 right nowrap">{{ Format::duration($leg->durationMinutes) }}</td>
            </tr>
        </table>
        <table class="times full">
            <tr>
                <td class="time w8">{{ Format::time($leg->departureAt) }}</td>
                <td class="dot"><img class="stop" src="{{ Icon::stop('from') }}" alt=""></td>
                <td class="station w6">{{ $leg->fromStation }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="dot"><div class="connector"></div></td>
                <td></td>
            </tr>
            <tr>
                <td class="time w8">{{ Format::time($leg->arrivalAt) }}</td>
                <td class="dot"><img class="stop" src="{{ Icon::stop($final ? 'to' : 'via') }}" alt=""></td>
                <td @class(['station', 'w8' => $final, 'w6' => ! $final])>
                    {{ $leg->toStation }}
                    @if ($leg->departureAt && $leg->arrivalAt && ! $leg->arrivalAt->isSameDay($leg->departureAt))
                        <span class="muted w6">(volgende dag)</span>
                    @endif
                </td>
            </tr>
        </table>
        @if ($leg->carriage || $leg->seat)
            <div class="badges">
                @if ($leg->carriage)<span class="badge w7">Rijtuig {{ $leg->carriage }}</span>@endif
                @if ($leg->seat)<span class="badge w7">Stoel {{ $leg->seat }}</span>@endif
            </div>
        @endif
        @if ($leg->note)
            <p class="muted" style="margin-top: 10px; font-size: 13px">{{ $leg->note }}</p>
        @endif
        @foreach ($infoLines as $infoLine)
            @include('travel::documents.parts.info-link', ['href' => $infoLine->infoLink, 'label' => 'Meer over '.$infoLine->name])
        @endforeach
    </div>
@else
    @php
        $line = $leg->transitLines[0] ?? null;
        $from = array_filter([
            $leg->fromStation ? 'Vanaf '.$leg->fromStation : null,
            $line?->direction ? 'richting '.$line->direction : null,
        ]);
    @endphp
    <div class="hop">
        <table class="full">
            <tr>
                <td style="width: 32px">
                    @if ($line)
                        <div class="line-dot w8" style="background-color: {{ $line->color ?? '#1f3640' }}" role="img"
                            aria-label="{{ $line->name }}">{{ Format::lineBadge($line->name) }}</div>
                    @else
                        <img class="icon-20" src="{{ Icon::src($leg->type === LegType::Walk ? 'walk' : 'train') }}" alt="">
                    @endif
                </td>
                <td class="text">
                    <p class="hop-title w7">{{ $leg->type->label() }}@if ($leg->toStation) naar {{ $leg->toStation }}@endif</p>
                    @if ($from !== [])
                        <p class="hop-sub">{{ ucfirst(implode(', ', $from)) }}</p>
                    @endif
                    @if ($leg->note)
                        <p class="hop-sub">{{ $leg->note }}</p>
                    @endif
                    @foreach ($infoLines as $infoLine)
                        @include('travel::documents.parts.info-link', ['href' => $infoLine->infoLink, 'label' => 'Meer over '.$infoLine->name])
                    @endforeach
                </td>
                @if ($leg->durationMinutes !== null)
                    <td class="right nowrap">
                        <p class="hop-duration w8">{{ Format::duration($leg->durationMinutes) }}</p>
                        <p class="hop-label">overstap</p>
                    </td>
                @endif
            </tr>
        </table>
    </div>
@endif
