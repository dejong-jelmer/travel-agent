<?php

namespace App\Travel\Data;

use App\Models\Booking;
use App\Travel\Models\City;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use App\Travel\Models\Stay;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Everything a travel document shows, gathered from a booking. Documents render against this read model instead of
 * the models, so a document can be tested without a booking in the database.
 */
readonly class TravelDocumentData
{
    /**
     * @param  list<string>  $travelerNames
     * @param  list<JourneyData>  $journeys
     * @param  list<StayData>  $stays
     * @param  list<CityData>  $cities  The cities the trip passes through, each with its pharmacies.
     */
    public function __construct(
        public string $title,
        public array $travelerNames,
        public array $journeys,
        public array $stays,
        public array $cities,
    ) {}

    /**
     * The caller passes the title and the traveler names, so the module never reaches into the trip or its travelers.
     *
     * @param  list<string>  $travelerNames
     */
    public static function fromBooking(Booking $booking, string $title, array $travelerNames): self
    {
        $booking->loadMissing([
            'journeys.legs.fromStation.city',
            'journeys.legs.toStation.city',
            'journeys.legs.transitLines',
            'stays.accommodation.city',
            'stays.accommodation.legs.fromStation',
            'stays.accommodation.legs.toStation',
            'stays.accommodation.legs.transitLines',
        ]);

        return new self(
            title: $title,
            travelerNames: $travelerNames,
            journeys: $booking->journeys->map(fn (Journey $journey) => JourneyData::fromModel($journey))->all(),
            stays: $booking->stays->map(fn (Stay $stay) => StayData::fromModel($stay))->all(),
            cities: self::citiesOf($booking)->map(fn (City $city) => CityData::fromModel($city))->all(),
        );
    }

    /**
     * The cities of the journey stations and of the stays, in order of appearance. Arrival routes are left out: they
     * run within the city of their accommodation.
     *
     * @return EloquentCollection<int, City>
     */
    private static function citiesOf(Booking $booking): EloquentCollection
    {
        $cities = $booking->journeys
            ->flatMap(fn (Journey $journey) => $journey->legs)
            ->flatMap(fn (Leg $leg) => [$leg->fromStation?->city, $leg->toStation?->city])
            ->merge($booking->stays->map(fn (Stay $stay) => $stay->accommodation->city))
            ->filter()
            ->unique('id')
            ->values();

        return (new EloquentCollection($cities))->load('pharmacies');
    }
}
