{{-- The legs of a journey, or of an arrival route, which has no times ($timed false). --}}
@use('App\Travel\Documents\Format')
<table class="legs">
    @foreach ($legs as $leg)
        <tr>
            @if ($timed)
                {{-- The times are spans, so the page for the phone can put them under each other. --}}
                <td class="legs-time">
                    {!! implode('<span class="legs-to"> – </span>', array_map(
                        fn (string $time) => "<span>{$time}</span>",
                        array_filter([Format::time($leg->departureAt), Format::time($leg->arrivalAt)]),
                    )) !!}
                    @if ($leg->departureAt && $leg->arrivalAt && ! $leg->arrivalAt->isSameDay($leg->departureAt))
                        <span class="muted">(volgende dag)</span>
                    @endif
                </td>
            @endif
            <td class="legs-what">
                <strong>{{ $leg->type->label() }}</strong>@if ($stations = Format::stations($leg->fromStation, $leg->toStation)) · {{ $stations }}@endif
                @foreach ($leg->transitLines as $line)
                    <div class="legs-line">
                        <span class="dot" style="background-color: {{ $line->color ?? '#c8cdd2' }}"></span>
                        {{ $line->name }}@if ($line->direction) <span class="muted">richting {{ $line->direction }}</span>@endif
                    </div>
                @endforeach
                @if ($ticket = Format::ticket($leg->number, $leg->carriage, $leg->seat))
                    <div class="muted">{{ $ticket }}</div>
                @endif
                @if ($leg->note)
                    <div class="note">{{ $leg->note }}</div>
                @endif
            </td>
            <td class="legs-duration">{{ Format::duration($leg->durationMinutes) }}</td>
        </tr>
    @endforeach
</table>
