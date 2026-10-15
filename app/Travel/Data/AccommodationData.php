<?php

namespace App\Travel\Data;

use App\Travel\Enums\AccommodationType;
use App\Travel\Models\Accommodation;
use App\Travel\Models\Leg;

readonly class AccommodationData
{
    /**
     * @param  list<LegData>  $arrivalRoute  From the station to the door, the same for every booking.
     */
    public function __construct(
        public string $name,
        public AccommodationType $type,
        public string $city,
        public ?string $address,
        public ?string $phone,
        public ?string $latitude,
        public ?string $longitude,
        public ?string $checkIn,
        public ?string $checkOut,
        public ?string $mapImage,
        public ?string $walkDirections,
        public array $arrivalRoute,
    ) {}

    public static function fromModel(Accommodation $accommodation): self
    {
        return new self(
            name: $accommodation->name,
            type: $accommodation->type,
            city: $accommodation->city->name,
            address: $accommodation->address,
            phone: $accommodation->phone,
            latitude: $accommodation->latitude,
            longitude: $accommodation->longitude,
            checkIn: $accommodation->check_in,
            checkOut: $accommodation->check_out,
            mapImage: $accommodation->map_image,
            walkDirections: $accommodation->walk_directions,
            arrivalRoute: $accommodation->legs->map(fn (Leg $leg) => LegData::fromModel($leg))->all(),
        );
    }
}
