<?php

namespace App\Services;

use App\DTO\ContactDetails;
use InvalidArgumentException;

class ContactDetailsService
{
    public function __construct(
        protected ContactDetails $details
    ) {}

    public function fullAddress(): string
    {
        return $this->details->address.PHP_EOL.$this->details->postal.' '.$this->details->city;
    }

    /**
     * Retrieves a specific contact detail based on the provided key.
     *
     * @param  string  $detail  The key of the desired contact detail (e.g., 'telephone', 'email', etc.).
     * @return PhoneNumberService|AntiSpamEmailService|string Returns a `PhoneNumberService` instance for 'telephone',
     *                                                        or a string for other details.
     *
     * @throws InvalidArgumentException If the given detail does not exist on the `$details` object.
     */
    public function getContact(string $detail): PhoneNumberService|AntiSpamEmailService|string
    {
        return match ($detail) {
            'telephone' => $this->getTelephone(),
            'mail' => $this->getSpamSafeEmail(),
            default => property_exists($this->details, $detail)
                ? $this->details->$detail
                : throw new InvalidArgumentException("Contact detail '$detail' is not a valid property of '".ContactDetails::class."'")
        };
    }

    public function getTelephone(): PhoneNumberService
    {
        return $this->details->telephone;
    }

    public function getSpamSafeEmail()
    {
        return $this->details->mail;
    }
}
