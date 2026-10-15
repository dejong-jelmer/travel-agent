<?php

namespace App\Travel\Data;

use App\Travel\Models\Pharmacy;

readonly class PharmacyData
{
    public function __construct(
        public string $name,
        public ?string $address,
    ) {}

    public static function fromModel(Pharmacy $pharmacy): self
    {
        return new self(
            name: $pharmacy->name,
            address: $pharmacy->address,
        );
    }
}
