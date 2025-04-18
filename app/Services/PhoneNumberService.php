<?php

namespace App\Services;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberService
{
    protected string $raw;

    protected $parsed;

    protected PhoneNumberUtil $util;

    public function __construct(string $phoneNumber)
    {
        $this->raw = trim($phoneNumber);
        $this->util = PhoneNumberUtil::getInstance();
        $locale = config('app.locale');
        $this->parsed = $this->util->parse($this->raw, $locale);
    }

    public function isValid(): bool
    {
        return $this->parsed && $this->util->isValidNumber($this->parsed);
    }

    public function forTelLink(): string
    {
        return $this->isValid()
            ? $this->util->format($this->parsed, PhoneNumberFormat::E164)
            : $this->raw;
    }

    public function forDisplay(): string
    {
        return $this->isValid()
            ? $this->util->format($this->parsed, PhoneNumberFormat::INTERNATIONAL)
            : $this->raw;
    }
}
