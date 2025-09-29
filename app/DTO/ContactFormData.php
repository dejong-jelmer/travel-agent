<?php

namespace App\DTO;

class ContactFormData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $text,
        public ?string $phone = null
    ) {}
}
