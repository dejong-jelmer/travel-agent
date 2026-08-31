<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use App\DTO\Traits\BookingDataParser;
use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingData implements Arrayable
{
    use ArrayableDTO, BookingDataParser;

    public function __construct(
        public readonly Status $status,
        public readonly PaymentStatus $payment_status,
        public readonly array $main_booker,
        public readonly array $travelers,
        public readonly BookingContactData $contact,
        public readonly ?string $internal_notes,
        public readonly ?string $return_date,
        public readonly ?array $cost_items,
        public readonly ?int $margin_basis_points,
        public readonly ?bool $margin_in_percentage,
        public readonly ?int $margin_amount,
        public readonly ?int $fee_per_person,
        public readonly ?int $final_price,
    ) {}

    /**
     * Create from validated request
     */
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();
        $parsed = self::parseValidatedData($validated);

        return new self(
            status: Status::from($validated['status']),
            payment_status: PaymentStatus::from($validated['payment_status']),
            main_booker: $parsed['main_booker'],
            travelers: $parsed['travelers'],
            contact: $parsed['contact'],
            internal_notes: $validated['internal_notes'] ?? null,
            return_date: $validated['return_date'] ?? null,
            cost_items: array_key_exists('cost_items', $validated) ? $parsed['cost_items'] : null,
            margin_basis_points: array_key_exists('margin_percentage', $validated) ? $parsed['margin_basis_points'] : null,
            margin_in_percentage: array_key_exists('margin_in_percentage', $validated) ? $parsed['margin_in_percentage'] : null,
            margin_amount: array_key_exists('margin_amount', $validated) ? $parsed['margin_amount'] : null,
            fee_per_person: array_key_exists('fee_per_person', $validated) ? $parsed['fee_per_person'] : null,
            final_price: array_key_exists('final_price', $validated) ? $parsed['final_price'] : null,
        );
    }
}
