<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class BookingTravelerData implements Arrayable
{
    use ArrayableDTO;

    public function __construct(
        public readonly ?int $id,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $full_name,
        public readonly Carbon $birthdate,
        public readonly string $nationality,
    ) {}

    /**
     * @param  array<string,string>  $data
     * @return self
     */
    public static function fromArray(array $data, $toArray = true): self|array
    {
        $traveler = new self(
            $data['id'] ?? null,
            $data['first_name'],
            $data['last_name'],
            $data['full_name'],
            Carbon::parse($data['birthdate']),
            $data['nationality']
        );

        if ($toArray) {
            $traveler = $traveler->toArray();
        }

        return $traveler;
    }

    /**
     * @param  array<int,array<string,mixed>>  $travelers
     * @return array<int,self>
     */
    public static function manyFromArray(array $travelers): array
    {
        return array_map(fn ($tr) => self::fromArray($tr), $travelers);
    }
}
