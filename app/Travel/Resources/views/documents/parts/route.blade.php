{{-- The way from the station to the door: the legs of the arrival route as a line with its stops, then the walking
    directions as numbered steps. A stop without a station of its own is named by the note of the leg. --}}
@use('App\Travel\Documents\Format')
@use('App\Travel\Documents\Icon')
@use('App\Travel\Enums\LegType')
@php
    $legs = $accommodation->arrivalRoute;
    $start = $legs[0]->fromStation ?? null;
    $total = array_sum(array_map(fn ($leg) => $leg->durationMinutes ?? 0, $legs));
    $steps = Format::steps($accommodation->walkDirections);
    $infoLink = collect($legs)->flatMap(fn ($leg) => $leg->transitLines)->first(fn ($line) => $line->infoLink !== null)?->infoLink;
@endphp
<div class="route">
    <table class="full">
        <tr>
            <td class="route-title w8">{{ $start ? 'Vanaf '.$start : 'Vanaf het station' }}</td>
            @if ($total > 0)
                <td class="route-total w7 right nowrap">ca. {{ Format::duration($total) }}</td>
            @endif
        </tr>
    </table>

    @if ($legs !== [])
        <div class="line-route">
            <table class="full">
                @if ($start)
                    <tr class="stop-row">
                        <td colspan="2" style="width: 20px" class="center"><img class="stop" src="{{ Icon::stop('from') }}" alt=""></td>
                        <td class="label stop-name w7">{{ $start }}</td>
                    </tr>
                @endif
                @foreach ($legs as $leg)
                    <tr class="segment">
                        <td class="rail"></td>
                        <td class="rail-line"></td>
                        <td class="label">
                            @if ($leg->transitLines !== [])
                                <table class="segment-lines">
                                    <tr>
                                        @foreach ($leg->transitLines as $line)
                                            @if (! $loop->first)
                                                <td class="line-or">of</td>
                                            @endif
                                            <td style="width: 26px">
                                                <div class="line-tile w8" style="background-color: {{ $line->color ?? '#1f3640' }}" role="img"
                                                    aria-label="{{ $line->name }}">{{ Format::lineBadge($line->name) }}</div>
                                            </td>
                                            <td class="line-direction">{{ $line->direction ?? $line->name }}</td>
                                        @endforeach
                                    </tr>
                                </table>
                            @else
                                <span class="segment-lines w7">{{ $leg->type->label() }}</span>
                            @endif
                            @if ($leg->durationMinutes !== null)
                                <div class="segment-duration">{{ Format::duration($leg->durationMinutes) }}</div>
                            @endif
                        </td>
                    </tr>
                    @php
                        $name = match (true) {
                            $leg->toStation !== null => $leg->toStation,
                            $leg->note !== null => ucfirst($leg->note),
                            $loop->last => $accommodation->name,
                            default => null,
                        };
                    @endphp
                    @if ($name)
                        <tr class="stop-row">
                            <td colspan="2" class="center"><img class="stop" src="{{ Icon::stop($loop->last ? 'to' : 'via') }}" alt=""></td>
                            <td class="label">
                                <span @class(['stop-name', 'w8' => $loop->last, 'w7' => ! $loop->last])>{{ $name }}</span>
                                @if ($leg->toStation && $leg->note)
                                    <div class="stop-sub">{{ ucfirst($leg->note) }}</div>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    @endif

    @if ($infoLink)
        @include('travel::documents.parts.info-link', ['href' => $infoLink, 'label' => 'Bekijk actuele routes en tijden'])
    @endif

    @if ($steps !== [])
        <table class="steps full">
            @foreach ($steps as $step)
                <tr class="top-align">
                    <td class="number"><div @class(['number-circle w8', 'number-sun' => $loop->last])>{{ $loop->iteration }}</div></td>
                    <td class="step">{{ $step }}</td>
                </tr>
            @endforeach
        </table>
    @endif
</div>
