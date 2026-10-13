{{-- The check-in and check-out of an accommodation, as two tiles. --}}
@use('App\Travel\Documents\Format')
<table class="tiles full">
    <tr>
        <td class="first">
            <div class="tile">
                <p class="tile-label">Check-in</p>
                <p class="tile-value w8">{{ $accommodation->checkIn ? 'vanaf '.Format::timeOfDay($accommodation->checkIn) : 'onbekend' }}</p>
            </div>
        </td>
        <td class="second">
            <div class="tile">
                <p class="tile-label">Check-out</p>
                <p class="tile-value w8">{{ $accommodation->checkOut ? 'tot '.Format::timeOfDay($accommodation->checkOut) : 'onbekend' }}</p>
            </div>
        </td>
    </tr>
</table>
