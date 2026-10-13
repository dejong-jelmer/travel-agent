<?php

namespace App\Travel\Data;

use App\Models\Booking;
use App\Travel\Models\City;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use App\Travel\Models\Stay;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Everything a travel document shows, gathered from a booking. Documents render against this read model instead of
 * the models, so a document can be tested without a booking in the database.
 */
readonly class TravelDocumentData
{
    /**
     * The country the travelers set out from and return to. The documents tell about the other countries.
     */
    public const HOME_COUNTRY = 'NL';

    /**
     * @param  list<string>  $travelerNames
     * @param  list<JourneyData>  $journeys
     * @param  list<StayData>  $stays
     * @param  list<CityData>  $cities  The cities the trip passes through, each with its pharmacies.
     * @param  list<CityData>  $stayCities  The cities the travelers stay in, in the order of the stays, each once: the
     *                                      places where a pharmacy is nearby.
     * @param  string|null  $emergencyPhone  The number the travelers call when they are stuck.
     */
    public function __construct(
        public string $title,
        public array $travelerNames,
        public array $journeys,
        public array $stays,
        public array $cities,
        public array $stayCities,
        public ?string $emergencyPhone = null,
    ) {}

    /**
     * The caller passes what lives outside the module: the title, the traveler names and the emergency number. So the
     * module never reaches into the trip, its travelers or the app's contact details.
     *
     * @param  list<string>  $travelerNames
     */
    public static function fromBooking(Booking $booking, string $title, array $travelerNames, ?string $emergencyPhone = null): self
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
            stayCities: self::stayCitiesOf($booking)->map(fn (City $city) => CityData::fromModel($city))->all(),
            emergencyPhone: $emergencyPhone,
        );
    }

    /**
     * The first day of the trip: the earliest journey or arrival.
     */
    public function firstDay(): ?CarbonInterface
    {
        $days = array_filter([
            ...array_map(fn (JourneyData $journey) => $journey->date, $this->journeys),
            ...array_map(fn (StayData $stay) => $stay->arrival, $this->stays),
        ]);

        return $days === [] ? null : min($days);
    }

    /**
     * The last day of the trip: the latest journey or departure.
     */
    public function lastDay(): ?CarbonInterface
    {
        $days = array_filter([
            ...array_map(fn (JourneyData $journey) => $journey->date, $this->journeys),
            ...array_map(fn (StayData $stay) => $stay->departure, $this->stays),
        ]);

        return $days === [] ? null : max($days);
    }

    public function nights(): int
    {
        return array_sum(array_map(fn (StayData $stay) => $stay->nights ?? 0, $this->stays));
    }

    /**
     * The countries the trip goes to, in order of appearance, as ISO codes. The home country is left out: the
     * practical information and the emergency numbers are about abroad.
     *
     * @return list<string>
     */
    public function destinations(): array
    {
        $countries = array_map(fn (CityData $city) => $city->country, $this->cities);

        return array_values(array_unique(array_diff($countries, [self::HOME_COUNTRY])));
    }

    /**
     * Whether any leg runs on a line of this name, such as Eurostar, whose advice the documents add.
     */
    public function usesLine(string $name): bool
    {
        foreach ($this->journeys as $journey) {
            foreach ($journey->legs as $leg) {
                foreach ($leg->transitLines as $line) {
                    if (stripos($line->name, $name) !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * The journeys and stays in the order of the trip. The two lists are merged by date, each keeping its own order:
     * position orders within a list, the date between them. A journey goes before a stay that starts on its day, as
     * the travelers arrive before they check in. Whatever has no date waits for the other list.
     *
     * @return list<JourneyData|StayData>
     */
    public function schedule(): array
    {
        $journeys = $this->journeys;
        $stays = $this->stays;
        $schedule = [];

        while ($journeys !== [] && $stays !== []) {
            $journeyDate = $journeys[0]->date;
            $stayDate = $stays[0]->arrival;

            $schedule[] = $stayDate === null || ($journeyDate !== null && $journeyDate->lte($stayDate))
                ? array_shift($journeys)
                : array_shift($stays);
        }

        return [...$schedule, ...$journeys, ...$stays];
    }

    /**
     * The cities of the journey stations and of the stays, in order of appearance, with their pharmacies. Arrival
     * routes are left out: they run within the city of their accommodation.
     *
     * Expects the stations and accommodations of the booking to be loaded with their city.
     *
     * @return EloquentCollection<int, City>
     */
    public static function citiesOf(Booking $booking): EloquentCollection
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

    /**
     * The cities of the stays, in the order of the stays, each once, with their pharmacies: the places where a
     * pharmacy is nearby. A city the trip only passes through has none to show.
     *
     * Expects the accommodations of the stays to be loaded with their city.
     *
     * @return EloquentCollection<int, City>
     */
    public static function stayCitiesOf(Booking $booking): EloquentCollection
    {
        $cities = $booking->stays->map(fn (Stay $stay) => $stay->accommodation->city)->filter()->unique('id')->values();

        return (new EloquentCollection($cities))->load('pharmacies');
    }
}
