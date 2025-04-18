<?php

namespace App\DTO;

class ContactFromData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $text,
        public ?string $telephone = null
    ) {}
}
