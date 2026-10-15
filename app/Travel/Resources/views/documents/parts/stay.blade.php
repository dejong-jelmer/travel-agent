{{-- A stay in the schedule: where the travelers sleep, with a link to the way there in the Verblijf part. --}}
@use('App\Travel\Documents\Format')
@use('App\Travel\Enums\LegType')
@php($accommodation = $stay->accommodation)
@php($by = collect($accommodation->arrivalRoute)->first(fn ($leg) => in_array($leg->type, [LegType::Tram, LegType::Bus, LegType::Metro], true))?->type)
<div class="stay">
    <table class="full">
        <tr>
            <td><span class="kicker w8 sun">Verblijf @if ($stay->nights !== null)· {{ Format::nights($stay->nights) }}@endif</span></td>
            <td class="right nowrap" style="font-size: 12px; color: #c9d6da">{{ Format::shortPeriod($stay->arrival, $stay->departure) }}</td>
        </tr>
    </table>
    <p class="stay-name w8">{{ $accommodation->name }}</p>
    @if ($accommodation->address)
        <p class="stay-address">{!! implode('<br>', array_map('e', Format::addressLines($accommodation->address))) !!}</p>
    @endif
    @if ($accommodation->checkIn || $accommodation->checkOut)
        @include('travel::documents.parts.check-times', ['accommodation' => $accommodation])
    @endif
    <table class="stay-links full">
        <tr>
            <td>
                @if ($accommodation->phone)
                    <a class="w6" href="{{ Format::phoneUrl($accommodation->phone) }}">{{ $accommodation->phone }}</a>
                @endif
            </td>
            <td class="right nowrap">
                <a class="route-to w7" href="{{ $stayHref($stayNumber) }}">
                    {{ $by ? 'Route met de '.mb_strtolower($by->label()) : 'Route vanaf het station' }} →
                </a>
            </td>
        </tr>
    </table>
</div>
