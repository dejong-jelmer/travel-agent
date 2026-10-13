{{-- The Verblijf part: per stay the accommodation, and the way to it from the station. --}}
@use('App\Travel\Documents\Format')
@forelse ($document->stays as $stay)
    @php($accommodation = $stay->accommodation)
    <section @class(['block', 'block-first' => $loop->first]) id="verblijf-{{ $loop->iteration }}">
        <div class="city">
            <span class="kicker w7">
                Verblijf {{ $loop->iteration }}@if ($period = Format::shortPeriod($stay->arrival, $stay->departure)) · {{ $period }}@endif @if ($stay->nights !== null)· {{ Format::nights($stay->nights) }}@endif
            </span>
            <h2 class="w8">{{ $accommodation->city }}</h2>
        </div>

        <div class="place">
            <p class="place-name w8">{{ $accommodation->name }}</p>
            @if ($accommodation->address || $accommodation->phone)
                <div class="place-contact">
                    @if ($accommodation->address)
                        <p><a href="{{ Format::mapUrl($accommodation->address) }}">{{ $accommodation->address }}</a></p>
                    @endif
                    @if ($accommodation->phone)
                        <p><a href="{{ Format::phoneUrl($accommodation->phone) }}">{{ $accommodation->phone }}</a></p>
                    @endif
                </div>
            @endif
            @if ($accommodation->checkIn || $accommodation->checkOut)
                @include('travel::documents.parts.check-times', ['accommodation' => $accommodation])
            @endif
        </div>

        @if ($accommodation->arrivalRoute !== [] || $accommodation->walkDirections)
            @include('travel::documents.parts.route', ['accommodation' => $accommodation])
        @endif
    </section>
@empty
    <p class="muted">De verblijven volgen nog.</p>
@endforelse
