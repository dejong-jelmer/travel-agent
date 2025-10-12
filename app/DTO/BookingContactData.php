<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use Illuminate\Contracts\Support\Arrayable;

class BookingContactData implements Arrayable
{
    use ArrayableDTO;

    public function __construct(
        public readonly string $name,
        public readonly string $street,
        public readonly string $house_number,
        public readonly ?string $addition,
        public readonly string $postal_code,
        public readonly string $city,
        public readonly string $email,
        public readonly string $phone
    ) {}

    /**
     * @param  array<string,string>  $data
     */
    public static function fromArray(string $name, array $data): self
    {
        return new self(
            $name,
            $data['street'],
            $data['house_number'],
            $data['addition'],
            $data['postal_code'],
            $data['city'],
            $data['email'],
            $data['phone'],
        );
    }
}
