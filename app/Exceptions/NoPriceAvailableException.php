<?php

namespace App\Exceptions;

use App\Models\Trip;
use Carbon\Carbon;
use Exception;

class NoPriceAvailableException extends Exception
{
    public function __construct(
        private readonly int $tripId,
        private readonly string $departureDate,
        string $message = '',
    ) {
        parent::__construct(
            $message ?: "No prices available for trip id: \"{$this->tripId}\" on departure date: \"{$this->departureDate}\"."
        );
    }

    public static function for(Trip $trip, Carbon $departureDate): self
    {
        return new self($trip->id, $departureDate->toDateString());
    }
}
