{{-- Part 1: the journeys and the nights in between, in the order of the trip. --}}
@use('App\Travel\Data\JourneyData')
@use('App\Travel\Documents\Format')
<section class="part">
    <h2>Reisschema</h2>
    @forelse ($document->schedule() as $entry)
        @if ($entry instanceof JourneyData)
            <div class="journey">
                <h3>{{ $entry->label }}@if ($entry->date) <span class="muted">· {{ Format::date($entry->date) }}</span>@endif</h3>
                @include('travel::documents.parts.legs', ['legs' => $entry->legs, 'timed' => true])
            </div>
        @else
            <div class="stay">
                <strong>Overnachten</strong> in {{ $entry->accommodation->name }}, {{ $entry->accommodation->city }}
                <div class="muted">
                    {{ Format::shortDate($entry->arrival) }} – {{ Format::shortDate($entry->departure) }}@if ($entry->nights !== null) · {{ Format::nights($entry->nights) }}@endif
                </div>
            </div>
        @endif
    @empty
        <p class="muted">Het reisschema volgt nog.</p>
    @endforelse
</section>
