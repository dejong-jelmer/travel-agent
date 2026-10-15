{{-- The Praktisch part: what to arrange before leaving, and what helps on the way. What depends on the country
    follows from the countries of the trip, see Country. --}}
@use('App\Travel\Data\CityData')
@use('App\Travel\Documents\Country')
@use('App\Travel\Documents\Format')
@use('App\Travel\Documents\Icon')
@php
    $countries = Country::all($document->destinations());
    $names = Format::list(array_map(fn (Country $country) => $country->name, $countries));
    $idCard = $countries !== [] && collect($countries)->every(fn (Country $country) => $country->idCardSuffices);
    $ehic = $countries !== [] && collect($countries)->every(fn (Country $country) => $country->ehic);
    $water = array_values(array_filter($countries, fn (Country $country) => $country->drinkingWater !== null));
    $summer = in_array($document->firstDay()?->month, [5, 6, 7, 8, 9], true);
    // The pharmacies near the stays: a city the travelers only pass through has none to show.
    $pharmacies = array_values(array_filter($document->stayCities, fn (CityData $city) => $city->pharmacies !== []));
    $checklist = array_filter([
        $idCard ? 'Geldig paspoort of ID-kaart' : 'Geldig paspoort',
        'Reis- en annuleringsverzekering',
        $ehic ? 'Europese zorgpas (EHIC)' : null,
        'Tickets offline op je telefoon en geprint',
    ]);
@endphp

<section class="block block-first checklist">
    <span class="kicker w8 sun">Voor vertrek</span>
    <h2 class="w8">Heb je alles geregeld?</h2>
    <table class="full" style="margin-top: 10px">
        @foreach ($checklist as $item)
            <tr @class(['item', 'item-last' => $loop->last])>
                <td class="box">
                    @if ($medium === 'web')
                        <input type="checkbox" id="check-{{ $loop->iteration }}" style="width: 20px; height: 20px; margin: 0; accent-color: #f2a516">
                    @else
                        <div class="checkbox"></div>
                    @endif
                </td>
                <td><label for="check-{{ $loop->iteration }}">{{ $item }}</label></td>
            </tr>
        @endforeach
    </table>
</section>

@if ($countries !== [])
    <section class="block">
        @include('travel::documents.parts.heading', ['icon' => 'document', 'title' => 'Documenten'])
        <div class="box-card">
            <p class="box-title w8">{{ $idCard ? 'Paspoort of ID-kaart' : 'Paspoort' }}</p>
            <p class="box-text">
                Voor {{ $names }} heb je {{ $idCard ? 'een geldig paspoort of een geldige ID-kaart' : 'een geldig paspoort' }} nodig.
                Let op: je document moet nog geldig zijn op de dag dat je
                <strong>{{ count($countries) === 1 ? $names.' weer verlaat' : 'weer thuiskomt' }}</strong>.
            </p>
        </div>
    </section>
@endif

<section class="block">
    @include('travel::documents.parts.heading', ['icon' => 'shield', 'title' => 'Verzekering'])
    <p class="lead">Een reis- en annuleringsverzekering regel je zelf, vóór vertrek.</p>
    <table class="pair full">
        <tr>
            <td class="first">
                <div class="box-card">
                    <p class="box-title w8">Reisverzekering</p>
                    <p class="box-text">Dekt medische kosten, ongevallen en bagage onderweg.</p>
                </div>
            </td>
            <td class="second">
                <div class="box-card">
                    <p class="box-title w8">Annuleringsverzekering</p>
                    <p class="box-text">Vergoedt je reissom als je niet kunt vertrekken, bijvoorbeeld door ziekte.</p>
                </div>
            </td>
        </tr>
    </table>
    <div class="tip" style="margin-top: 12px">
        <table class="full">
            <tr class="top-align">
                <td style="width: 20px"><img class="icon-20" src="{{ Icon::src('alert', '#7a4a00') }}" alt=""></td>
                <td class="text">Sluit de annuleringsverzekering af <strong>op het moment dat je boekt</strong>. Daarna kan het meestal niet meer.</td>
            </tr>
        </table>
    </div>
    @if ($ehic)
        <div class="box-card">
            <table class="ehic-title">
                <tr>
                    <td><span class="ehic w8">EHIC</span></td>
                    <td class="box-title w8">Europese zorgpas</td>
                </tr>
            </table>
            <p class="box-text">
                Geeft je toegang tot de publieke zorg in {{ $names }}, op dezelfde voorwaarden als inwoners. Je vraagt hem gratis
                aan bij je zorgverzekeraar; vaak staat hij al op de achterkant van je zorgpas. Een reisverzekering blijft nodig voor
                wat de publieke zorg niet dekt.
            </p>
        </div>
    @endif
</section>

@if ($water !== [] || $summer)
    <section class="block">
        @include('travel::documents.parts.heading', ['icon' => 'drop', 'title' => 'Gezondheid'])
        @if ($water !== [])
            <div class="box-card">
                <p class="box-title w8">Kraanwater is prima te drinken</p>
                <p class="box-text">Bij waterpunten en fonteinen hangt meestal een bordje. Let daarop:</p>
                @foreach ($water as $country)
                    @if (count($water) > 1)
                        <p class="kicker w7" style="margin-top: 12px">{{ $country->name }}</p>
                    @endif
                    <table class="water full">
                        <tr>
                            <td class="first">
                                <div class="water-yes">
                                    <p class="w8">{{ $country->drinkingWater[0] }}</p>
                                    <p class="meaning" style="color: #2c4a3a">drinkwater</p>
                                </div>
                            </td>
                            <td class="second">
                                <div class="water-no">
                                    <p class="w8">{{ $country->drinkingWater[1] }}</p>
                                    <p class="meaning" style="color: #5c2a1e">geen drinkwater</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                @endforeach
            </div>
        @endif
        @if ($summer)
            <div class="box-card">
                <table class="full">
                    <tr class="top-align">
                        <td style="width: 22px"><img class="icon-22" src="{{ Icon::src('sun', '#b86e00') }}" alt=""></td>
                        <td style="padding-left: 12px; font-size: 14px; line-height: 1.55">
                            Bij warm weer wandel je het prettigst in de ochtend. Neem water, een hoed en zonnebrand mee voor de uren in de
                            volle zon.
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    </section>
@endif

@if ($pharmacies !== [])
    <section class="block">
        @include('travel::documents.parts.heading', ['icon' => 'cross', 'title' => 'Apotheken in de buurt'])
        <div class="pharmacies">
            @foreach ($pharmacies as $city)
                @foreach ($city->pharmacies as $pharmacy)
                    <div @class(['pharmacy', 'pharmacy-last' => $loop->parent->last && $loop->last])>
                        <span class="kicker w7">{{ $city->name }}</span>
                        <p class="pharmacy-name w8">{{ $pharmacy->name }}</p>
                        @if ($pharmacy->address)
                            <a href="{{ Format::mapUrl($pharmacy->address.', '.$city->name) }}">{{ $pharmacy->address }}</a>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    </section>
@endif
