{{-- A journey: its day and route, then its legs. The arrival of the last ride is the end of the day. --}}
@use('App\Travel\Documents\Format')
@php($lastRide = collect($journey->legs)->last(fn ($leg) => $leg->arrivalAt !== null))
<section>
    <table class="full journey-head">
        <tr>
            @if ($journey->date)
                @php($day = Format::day($journey->date))
                <td style="width: 56px">
                    <div class="day">
                        <div class="day-weekday w8">{{ $day['weekday'] }}</div>
                        <div class="day-number w8">{{ $day['day'] }}</div>
                        <div class="day-month w6">{{ $day['month'] }}</div>
                    </div>
                </td>
            @endif
            <td @class(['journey-title' => $journey->date])>
                <span class="kicker w7">{{ $journey->label }}</span>
                @if ($journey->from && $journey->to)
                    <h2 class="w8">{{ $journey->from }} → {{ $journey->to }}</h2>
                @endif
                @if ($journey->departsAt() && $journey->arrivesAt())
                    <p class="journey-summary">
                        {{ Format::time($journey->departsAt()) }} – {{ Format::time($journey->arrivesAt()) }} · {{ Format::changes($journey->changes()) }}
                    </p>
                @endif
            </td>
        </tr>
    </table>

    @foreach ($journey->legs as $leg)
        @include('travel::documents.parts.leg', ['leg' => $leg, 'final' => $leg === $lastRide])
    @endforeach
</section>
