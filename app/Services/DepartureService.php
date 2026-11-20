<?php

namespace App\Services;

use App\Enums\Booking\Status as BookingStatus;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\Trip;
use App\Support\MoneyHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DepartureService
{
    /**
     * Create a departure of a group trip from validated form data, guaranteed from the start when asked.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(Trip $trip, array $data): Departure
    {
        /** @var Departure $departure */
        $departure = $trip->departures()->create([
            ...$this->attributes($data),
            'guaranteed_at' => ($data['guaranteed'] ?? false) ? now() : null,
        ]);

        return $departure;
    }

    /**
     * Update a departure from validated form data. Its bookings hold a copy of the dates, which follows the
     * departure; every booking logs that as a change of its own.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Departure $departure, array $data): Departure
    {
        return DB::transaction(function () use ($departure, $data) {
            $departure = $this->lock($departure);
            $departure->update($this->attributes($data));

            if ($departure->wasChanged(['departs_on', 'returns_on'])) {
                $departure->bookings()->get()->each->update([
                    'departure_date' => $departure->departs_on,
                    'return_date' => $departure->returns_on,
                ]);
            }

            return $departure;
        });
    }

    /**
     * Cancel a departure for the given reason, and with it every booking that still holds places on it. It cannot
     * be undone.
     *
     * @return int the number of bookings that were canceled
     *
     * @throws ValidationException when the departure cannot be cancelled anymore
     */
    public function cancel(Departure $departure, string $reason): int
    {
        return DB::transaction(function () use ($departure, $reason) {
            $departure = $this->lock($departure);

            if (! $departure->canBeCancelled()) {
                throw ValidationException::withMessages(['reason' => __('departure.validation.not_cancellable')]);
            }

            $departure->update(['cancelled_at' => now(), 'cancellation_reason' => $reason]);

            // One by one through the model, so each booking logs its change of status
            $bookings = $departure->bookings()->whereIn('status', BookingStatus::seatOccupying())->get();
            $bookings->each(fn (Booking $booking) => $booking->update(['status' => BookingStatus::Canceled]));

            return $bookings->count();
        });
    }

    /**
     * Delete a departure that was never booked. A departure with bookings, also canceled or deleted ones, stays:
     * the bookings refer to it.
     *
     * @throws ValidationException when the departure has bookings
     */
    public function delete(Departure $departure): void
    {
        DB::transaction(function () use ($departure) {
            $departure = $this->lock($departure);

            if ($departure->bookings()->withTrashed()->exists()) {
                throw ValidationException::withMessages(['departure' => __('departure.validation.has_bookings')]);
            }

            $departure->delete();
        });
    }

    /**
     * The same row lock BookingService takes, so a change of the departure and a booking on it wait for each other.
     */
    private function lock(Departure $departure): Departure
    {
        return Departure::whereKey($departure->id)->lockForUpdate()->firstOrFail();
    }

    /**
     * The form holds euros and may leave the prices and the decision date empty.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(array $data): array
    {
        $cents = fn (mixed $euros) => filled($euros) ? MoneyHelper::toCents((string) $euros) : null;

        return [
            'departs_on' => $data['departs_on'],
            'returns_on' => $data['returns_on'],
            'min_participants' => $data['min_participants'],
            'max_participants' => $data['max_participants'],
            'price_per_person' => $cents($data['price_per_person'] ?? null),
            'single_supplement' => $cents($data['single_supplement'] ?? null),
            'decision_date' => filled($data['decision_date'] ?? null)
                ? $data['decision_date']
                : Departure::latestDecisionDateFor(
                    Carbon::parse($data['departs_on']),
                    Carbon::parse($data['returns_on'])
                ),
        ];
    }
}
