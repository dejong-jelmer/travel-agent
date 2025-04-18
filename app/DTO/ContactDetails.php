<?php

namespace App\DTO;
use App\Services\PhoneNumberService;

class ContactDetails
{
    public function __construct(
        public string $mail,
        public string $address,
        public string $postal,
        public string $city,
        public string $mapsLink,
        public PhoneNumberService $telephone
    ) {}
}
