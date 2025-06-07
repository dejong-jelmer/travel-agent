<?php

namespace App\DTO;

use App\Services\AntiSpamEmailService;
use App\Services\PhoneNumberService;

class ContactDetails
{
    public function __construct(
        public string $address,
        public string $postal,
        public string $city,
        public string $mapsLink,
        public AntiSpamEmailService $mail,
        public PhoneNumberService $telephone
    ) {}
}
