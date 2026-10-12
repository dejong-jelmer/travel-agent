{{-- Part 2: where the travelers sleep, and how they get there from the station. --}}
@use('App\Travel\Documents\Format')
<section class="part">
    <h2>Accommodaties</h2>
    @forelse ($document->stays as $stay)
        @php($accommodation = $stay->accommodation)
        <div class="accommodation">
            <h3>{{ $accommodation->name }}</h3>
            <p class="muted">{{ $accommodation->type->label() }} in {{ $accommodation->city }}</p>
            <table class="facts">
                <tr>
                    <th>Verblijf</th>
                    <td>
                        {{ Format::shortDate($stay->arrival) }} – {{ Format::shortDate($stay->departure) }}@if ($stay->nights !== null) · {{ Format::nights($stay->nights) }}@endif
                    </td>
                </tr>
                @if ($checkTimes = Format::checkTimes($accommodation->checkIn, $accommodation->checkOut))
                    <tr>
                        <th>In- en uitchecken</th>
                        <td>{{ $checkTimes }}</td>
                    </tr>
                @endif
                @if ($accommodation->address)
                    <tr>
                        <th>Adres</th>
                        <td>{{ $accommodation->address }}</td>
                    </tr>
                @endif
                @if ($accommodation->phone)
                    <tr>
                        <th>Telefoon</th>
                        <td>{{ $accommodation->phone }}</td>
                    </tr>
                @endif
            </table>
            @if ($accommodation->arrivalRoute !== [] || $accommodation->walkDirections)
                <h4>Van het station naar de deur</h4>
                @if ($accommodation->arrivalRoute !== [])
                    @include('travel::documents.parts.legs', ['legs' => $accommodation->arrivalRoute, 'timed' => false])
                @endif
                @if ($accommodation->walkDirections)
                    <p class="walk">{{ $accommodation->walkDirections }}</p>
                @endif
            @endif
        </div>
    @empty
        <p class="muted">De accommodaties volgen nog.</p>
    @endforelse
</section>
