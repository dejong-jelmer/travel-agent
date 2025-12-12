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
        $locale = app()->getLocale();
        $this->parsed = $this->util->parse($this->raw, $locale);
    }

    public function isValid(): bool
    {
        return $this->parsed && $this->util->isValidNumber($this->parsed);
    }

    public function getPhoneNumber(): string
    {
        return $this->isValid()
            ? $this->phoneToScrambledHex($this->util->format($this->parsed, PhoneNumberFormat::E164))
            : $this->raw;
    }

    private function phoneToScrambledHex(string $phone): string
    {
        return bin2hex(strrev($phone));
    }
}
