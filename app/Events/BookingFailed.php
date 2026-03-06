<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        private string $errorMessage,
        private string $errorContext,
        private ?array $bookingDetails = [],
    ) {}

    public function getErrorMessage(): string { return $this->errorMessage; }

    public function getErrorContext(): string { return $this->errorContext; }

    public function getBookingDetails(): ?array { return $this->bookingDetails; }
}
