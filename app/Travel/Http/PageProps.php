<?php

namespace App\Travel\Http;

use App\Models\Booking;
use App\Travel\Data\TravelDocumentData;
use App\Travel\Enums\AccommodationType;
use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Models\Accommodation;
use App\Travel\Models\City;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use App\Travel\Models\Pharmacy;
use App\Travel\Models\ShareLink;
use App\Travel\Models\Station;
use App\Travel\Models\Stay;
use App\Travel\Models\TransitLine;

/**
 * The props of the pages of the module: the travel plan of a booking and the library. Unlike the read model of the
 * documents, they carry the ids and the stored values the pages need to edit them.
 *
 * Dates go out as `Y-m-d`, times as `Y-m-d\TH:i` and times of day as `H:i`, without a time zone: they are
 * wall-clock times at the place itself, and the pages show them as they are.
 */
class PageProps
{
    /**
     * The sections of the library page, each with the props it edits.
     */
    public const LIBRARY_SECTIONS = ['cities', 'stations', 'lines', 'accommodations'];

    /**
     * @return array<string, mixed>
     */
    public static function forPlan(Booking $booking): array
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

        return [
            'booking' => [
                'id' => $booking->id,
                'reference' => $booking->reference,
                'departure_date' => $booking->departure_date->toDateString(),
                'return_date' => $booking->return_date->toDateString(),
            ],
            'journeys' => $booking->journeys->map(fn (Journey $journey) => [
                'id' => $journey->id,
                'label' => $journey->label,
                'date' => $journey->date?->toDateString(),
                'legs' => $journey->legs->map(fn (Leg $leg) => self::leg($leg))->all(),
            ])->all(),
            'stays' => $booking->stays->map(fn (Stay $stay) => [
                'id' => $stay->id,
                'arrival' => $stay->arrival?->toDateString(),
                'departure' => $stay->departure?->toDateString(),
                'nights' => $stay->nights,
                'accommodation' => self::accommodation($stay->accommodation),
            ])->all(),
            'cities' => TravelDocumentData::citiesOf($booking)->map(fn (City $city) => self::city($city))->all(),
            'shareLink' => self::shareLink($booking),
            'legTypeOptions' => self::legTypeOptions(),
            'accommodationTypeOptions' => self::accommodationTypeOptions(),
            'stations' => self::stations(),
            'transitLines' => self::transitLines(),
            'accommodations' => Accommodation::with('city')->orderBy('name')->get()->map(fn (Accommodation $accommodation) => [
                'id' => $accommodation->id,
                'name' => $accommodation->name,
                'city' => $accommodation->city->name,
            ])->all(),
        ];
    }

    /**
     * @param  value-of<self::LIBRARY_SECTIONS>  $section
     * @return array<string, mixed>
     */
    public static function forLibrary(string $section): array
    {
        return match ($section) {
            'cities' => [
                'cities' => City::with('pharmacies')->orderBy('name')->get()->map(fn (City $city) => self::city($city))->all(),
            ],
            'stations' => [
                'stations' => self::stations(),
                'cityOptions' => self::cityOptions(),
            ],
            'lines' => [
                'transitLines' => self::transitLines(),
                'transitModeOptions' => array_map(
                    fn (TransitMode $mode) => ['id' => $mode->value, 'name' => $mode->label()],
                    TransitMode::cases(),
                ),
            ],
            'accommodations' => [
                'accommodations' => Accommodation::with([
                    'city',
                    'legs.fromStation',
                    'legs.toStation',
                    'legs.transitLines',
                ])->orderBy('name')->get()->map(fn (Accommodation $accommodation) => self::accommodation($accommodation))->all(),
                'cityOptions' => self::cityOptions(),
                'legTypeOptions' => self::legTypeOptions(),
                'accommodationTypeOptions' => self::accommodationTypeOptions(),
                'stations' => self::stations(),
                'transitLines' => self::transitLines(),
            ],
        };
    }

    /**
     * @return array{url: string, expires_on: string, expired: bool}|null
     */
    private static function shareLink(Booking $booking): ?array
    {
        $link = ShareLink::firstWhere('booking_id', $booking->id)?->setRelation('booking', $booking);

        return $link === null ? null : [
            'url' => route('travel.documents.shared', $link),
            'expires_on' => $link->expiresOn()->toDateString(),
            'expired' => $link->isExpired(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function city(City $city): array
    {
        return [
            'id' => $city->id,
            'name' => $city->name,
            'country' => $city->country,
            'pharmacies' => $city->pharmacies->map(fn (Pharmacy $pharmacy) => $pharmacy->only(['id', 'name', 'address']))->all(),
        ];
    }

    /**
     * @return list<array{id: int, name: string, hint: string}>
     */
    private static function cityOptions(): array
    {
        return City::orderBy('name')->get()
            ->map(fn (City $city) => ['id' => $city->id, 'name' => $city->name, 'hint' => $city->country])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function stations(): array
    {
        return Station::with('city')->orderBy('name')->get()->map(fn (Station $station) => [
            'id' => $station->id,
            'name' => $station->name,
            'city_id' => $station->city_id,
            'city' => $station->city->name,
            'latitude' => $station->latitude,
            'longitude' => $station->longitude,
        ])->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function transitLines(): array
    {
        return TransitLine::orderBy('name')->get()->map(fn (TransitLine $line) => [
            'id' => $line->id,
            'name' => $line->name,
            'mode' => $line->mode,
            'color' => $line->color,
            'info_link' => $line->info_link,
        ])->all();
    }

    /**
     * A leg type carries transit lines when it has a transit mode, as LegInvariants::checkTransitLines() checks.
     *
     * @return list<array{id: string, name: string, has_lines: bool}>
     */
    private static function legTypeOptions(): array
    {
        return array_map(fn (LegType $type) => [
            'id' => $type->value,
            'name' => $type->label(),
            'has_lines' => TransitMode::tryFrom($type->value) !== null,
        ], LegType::cases());
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    private static function accommodationTypeOptions(): array
    {
        return array_map(
            fn (AccommodationType $type) => ['id' => $type->value, 'name' => $type->label()],
            AccommodationType::cases(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function accommodation(Accommodation $accommodation): array
    {
        return [
            'id' => $accommodation->id,
            'name' => $accommodation->name,
            'type' => $accommodation->type,
            'city_id' => $accommodation->city_id,
            'city' => $accommodation->city->name,
            'address' => $accommodation->address,
            'phone' => $accommodation->phone,
            'latitude' => $accommodation->latitude,
            'longitude' => $accommodation->longitude,
            'check_in' => self::timeOfDay($accommodation->check_in),
            'check_out' => self::timeOfDay($accommodation->check_out),
            'walk_directions' => $accommodation->walk_directions,
            'arrival_route' => $accommodation->legs->map(fn (Leg $leg) => self::leg($leg))->all(),
        ];
    }

    /**
     * A time column comes out of the database as `H:i:s`.
     */
    private static function timeOfDay(?string $time): ?string
    {
        return $time === null ? null : substr($time, 0, 5);
    }

    /**
     * @return array<string, mixed>
     */
    private static function leg(Leg $leg): array
    {
        return [
            'id' => $leg->id,
            'type' => $leg->type,
            'from_station' => $leg->fromStation?->only(['id', 'name']),
            'to_station' => $leg->toStation?->only(['id', 'name']),
            'departure_at' => $leg->departure_at?->format('Y-m-d\TH:i'),
            'arrival_at' => $leg->arrival_at?->format('Y-m-d\TH:i'),
            'duration' => $leg->duration,
            'duration_minutes' => $leg->duration_minutes,
            'number' => $leg->number,
            'carriage' => $leg->carriage,
            'seat' => $leg->seat,
            'note' => $leg->note,
            'transit_lines' => $leg->transitLines->map(fn (TransitLine $line) => [
                'id' => $line->id,
                'name' => $line->name,
                'color' => $line->color,
                'direction' => $line->pivot->direction,
            ])->all(),
        ];
    }
}
