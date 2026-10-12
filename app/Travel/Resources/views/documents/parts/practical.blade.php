{{-- Part 3: practical information, for now the pharmacies in the places of the trip. --}}
@php($citiesWithPharmacies = array_filter($document->cities, fn ($city) => $city->pharmacies !== []))
<section class="part">
    <h2>Praktische informatie</h2>
    <h3>Apotheken</h3>
    @forelse ($citiesWithPharmacies as $city)
        <div class="city">
            <strong>{{ $city->name }}</strong>
            @foreach ($city->pharmacies as $pharmacy)
                <div>{{ $pharmacy->name }}@if ($pharmacy->address) <span class="muted">· {{ $pharmacy->address }}</span>@endif</div>
            @endforeach
        </div>
    @empty
        <p class="muted">Er zijn nog geen apotheken bekend voor de plaatsen op de route.</p>
    @endforelse
</section>
