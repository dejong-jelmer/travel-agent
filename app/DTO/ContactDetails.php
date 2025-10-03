<?php

namespace App\DTO;

use App\Services\AntiSpamEmailService;
use App\Services\PhoneNumberService;

class ContactDetails
{
    public function __construct(
        public string $address,
        public string $postal_code,
        public string $city,
        public string $mapsLink,
        public string $kvk,
        public string $btw,
        public AntiSpamEmailService $mail,
        public PhoneNumberService $phone
    ) {}
}
