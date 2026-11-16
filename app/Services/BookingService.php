<?php

namespace App\Services;

use App\DTO\CreateBookingData;
use App\DTO\UpdateBookingData;
use App\Enums\TravelerType;
use App\Events\DepartureMinimumReached;
use App\Models\Booking;
use App\Models\Departure;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(private FeesAndFundsService $feesAndFunds) {}

    /**
     * Create a new booking with contact details, travelers, and cost items from validated DTO data.
     *
     * A booking of a group trip takes its places and its dates from the departure. The departure row stays locked
     * until the booking is saved, so two bookings made at the same moment cannot both get the last places.
     *
     * @param  CreateBookingData  $bookingData  Validated and typed booking input data.
     * @return Booking The newly created booking model.
     *
     * @throws ValidationException when the departure has no room for the travelers
     * @throws \Throwable when saving fails, which rolls the whole booking back
     */
    public function create(CreateBookingData $bookingData): Booking
    {
        return DB::transaction(fn () => $this->createInTransaction($bookingData));
    }

    private function createInTransaction(CreateBookingData $bookingData): Booking
    {
        // Get Data from DTO
        $contactData = $bookingData->contact->toArray();
        $travelersData = $bookingData->travelers;

        $totalAdults = $this->getTotalByType($bookingData->travelers, TravelerType::Adult);
        $totalChildren = $this->getTotalByType($bookingData->travelers, TravelerType::Child);

        $departure = $bookingData->departure ? $this->lockDeparture($bookingData->departure->id) : null;

        if ($departure) {
            $this->ensureDepartureHasRoom($departure, $totalAdults + $totalChildren, 'departure_id');
        }

        // Create booking
        /** @var Booking $booking */
        $booking = $bookingData->trip->bookings()->create([
            'departure_id' => $departure?->id,
            'departure_date' => $departure->departs_on ?? $bookingData->date,
            'return_date' => $departure->returns_on
                ?? $bookingData->return_date
                ?? $bookingData->date->copy()->addDays($bookingData->trip->duration ?? 0),
            'has_accepted_conditions' => $bookingData->has_accepted_conditions,
            'conditions_accepted_at' => $bookingData->has_accepted_conditions ? now() : null,
            'has_confirmed' => $bookingData->has_confirmed,
            'confirmed_at' => $bookingData->has_confirmed ? now() : null,
            'total_adults' => $totalAdults,
            'total_children' => $totalChildren,
            'margin_basis_points' => $bookingData->margin_basis_points,
            'margin_amount' => $bookingData->margin_amount,
            'fee_per_person' => $bookingData->fee_per_person,
            // Snapshot the current settings so later changes leave this booking untouched.
            'fees_and_funds' => $this->feesAndFunds->asCents(),
            'final_price' => $bookingData->final_price,
        ]);

        $booking->syncCostItems($bookingData->cost_items);

        // Create booking contact details
        $booking->contact()->create([
            'name' => $contactData['name'],
            'street' => $contactData['street'],
            'house_number' => $contactData['house_number'],
            'addition' => $contactData['addition'],
            'postal_code' => $contactData['postal_code'],
            'city' => $contactData['city'],
            'email' => $contactData['email'],
            'phone' => $contactData['phone'],
        ]);

        // Create the travelers
        $this->createTravelers($booking, $travelersData, $bookingData->main_booker['index']);

        return $booking;
    }

    /**
     * Update an existing booking's status, contact details, and travelers.
     *
     * A booking on a departure keeps the dates of the departure. Taking it out of canceled needs its places back, so
     * that is checked with the departure locked, like a new booking.
     *
     * @param  Booking  $booking  The booking to update.
     * @param  UpdateBookingData  $bookingData  Validated and typed update input data.
     * @return Booking The updated booking model.
     *
     * @throws ValidationException when the departure has no room to take the booking out of canceled
     */
    public function update(Booking $booking, UpdateBookingData $bookingData): Booking
    {
        // The booking, its cost items, contact and travelers are written as one
        // unit, so a failure part way through cannot leave a half-updated booking.
        return DB::transaction(function () use ($booking, $bookingData) {
            $departure = $booking->departure_id ? $this->lockDeparture($booking->departure_id) : null;
            $hadReachedMinimum = $departure?->hasReachedMinimum();

            if ($departure && ! $booking->status->occupiesSeat() && $bookingData->status->occupiesSeat()) {
                $this->ensureDepartureHasRoom($departure, $booking->total_adults + $booking->total_children, 'status');
            }

            $updateData = [
                'status' => $bookingData->status,
                'payment_status' => $bookingData->payment_status,
                'internal_notes' => $bookingData->internal_notes,
            ];

            if (! $departure) {
                $updateData['return_date'] = $bookingData->return_date;
            }

            if ($bookingData->margin_basis_points !== null) {
                $updateData['margin_basis_points'] = $bookingData->margin_basis_points;
            }

            if ($bookingData->margin_amount !== null) {
                $updateData['margin_amount'] = $bookingData->margin_amount;
            }

            if ($bookingData->fee_per_person !== null) {
                $updateData['fee_per_person'] = $bookingData->fee_per_person;
            }

            // A booking made before fees and funds existed has no snapshot to
            // protect, so capture one now. An existing snapshot is left alone.
            if ($booking->fees_and_funds === null) {
                $updateData['fees_and_funds'] = $this->feesAndFunds->asCents();
            }

            $updateData['final_price'] = $bookingData->final_price;

            $booking->update($updateData);

            if ($bookingData->cost_items !== null) {
                $booking->syncCostItems($bookingData->cost_items);
            }
            // Get data from DTO
            $contactData = $bookingData->contact->toArray();
            $travelersData = $bookingData->travelers;

            $booking->contact->update([
                'name' => $contactData['name'],
                'street' => $contactData['street'],
                'house_number' => $contactData['house_number'],
                'addition' => $contactData['addition'],
                'postal_code' => $contactData['postal_code'],
                'city' => $contactData['city'],
                'email' => $contactData['email'],
                'phone' => $contactData['phone'],
            ]);

            $this->updateOrCreateTravelers($booking, $travelersData, $bookingData->main_booker['index']);

            if ($departure && ! $hadReachedMinimum) {
                $this->notifyWhenMinimumIsReached($departure);
            }

            return $booking;
        });
    }

    /**
     * Read the departure again and lock its row until the transaction ends. Every write that takes places on a
     * departure goes through this lock, so their checks of the free places run one after the other.
     */
    private function lockDeparture(int $departureId): Departure
    {
        return Departure::whereKey($departureId)->lockForUpdate()->firstOrFail();
    }

    /**
     * @throws ValidationException under the given form field when the departure cannot take the travelers
     */
    private function ensureDepartureHasRoom(Departure $departure, int $travelers, string $field): void
    {
        $refusal = $departure->refusalToBook($travelers);

        if ($refusal !== null) {
            throw ValidationException::withMessages([$field => $refusal]);
        }
    }

    /**
     * Tell the admin, once the booking is saved, that this write brought the departure to its minimum. Only a
     * departure that still waits for the decision is reported: guaranteeing stays the admin's call.
     */
    private function notifyWhenMinimumIsReached(Departure $departure): void
    {
        if ($departure->isGuaranteed() || $departure->isCancelled() || ! $departure->hasReachedMinimum()) {
            return;
        }

        DB::afterCommit(fn () => event(new DepartureMinimumReached($departure)));
    }

    /**
     * Persist all travelers for a new booking and assign the main booker.
     *
     * @param  Booking  $booking  The booking to attach travelers to.
     * @param  array  $data  Travelers keyed by type (adults/children), each an array of traveler fields.
     * @param  int  $mainBookerIndex  Zero-based index of the adult traveler designated as main booker.
     */
    private function createTravelers(Booking $booking, array $data, int $mainBookerIndex): void
    {
        foreach ($data as $type => $travelers) {
            foreach ($travelers as $index => $travelerData) {
                /** @var \App\Models\BookingTraveler $travelerModel */
                $travelerModel = $booking->travelers()->create([
                    'type' => TravelerType::fromKey($type),
                    'first_name' => $travelerData['first_name'],
                    'last_name' => $travelerData['last_name'],
                    'birthdate' => $travelerData['birthdate'],
                    'nationality' => $travelerData['nationality'],
                    'special_requests' => $travelerData['special_requests'] ?? null,
                    'special_requests_consent' => $travelerData['special_requests_consent'],
                    'special_requests_consent_at' => $travelerData['special_requests_consent'] ? now() : null,
                ]);
                if ($mainBookerIndex === $index && $travelerModel->type === TravelerType::Adult) {
                    $booking->main_booker_id = $travelerModel->id;
                    $booking->saveQuietly();
                }
            }
        }
    }

    /**
     * Update or create travelers on an existing booking and reassign the main booker if needed.
     *
     * @param  Booking  $booking  The booking whose travelers are being updated.
     * @param  array  $data  Travelers keyed by type (adults/children), each an array of traveler fields.
     * @param  int  $mainBookerIndex  Zero-based index of the adult traveler designated as main booker.
     */
    private function updateOrCreateTravelers(Booking $booking, array $data, int $mainBookerIndex): void
    {
        foreach ($data as $travelers) {
            foreach ($travelers as $index => $travelerData) {
                $existing = $booking->travelers()->find($travelerData['id']);

                /** @var \App\Models\BookingTraveler $travelerModel */
                $travelerModel = $booking->travelers()->updateOrCreate(
                    ['id' => $travelerData['id']],
                    [
                        'first_name' => $travelerData['first_name'],
                        'last_name' => $travelerData['last_name'],
                        'birthdate' => $travelerData['birthdate'],
                        'nationality' => $travelerData['nationality'],
                        'special_requests' => $travelerData['special_requests'] ?? null,
                        'special_requests_consent' => $travelerData['special_requests_consent'] ?? false,
                        'special_requests_consent_at' => $travelerData['special_requests_consent']
                            ? ($existing->special_requests_consent_at ?? now())
                            : null,
                    ]
                );
                if ($mainBookerIndex === $index && $travelerModel->type === TravelerType::Adult) {
                    $booking->update([
                        'main_booker_id' => $travelerModel->id,
                    ]);
                }
            }
        }
    }

    /**
     * Count travelers of a specific type from a raw travelers array.
     *
     * @param  array  $travelers  Travelers keyed by type value (e.g. ['adults' => [...], 'children' => [...]]).
     * @param  TravelerType  $type  The traveler type to count.
     * @return int Number of travelers of the given type, or 0 if the key is absent.
     */
    public function getTotalByType(array $travelers, TravelerType $type): int
    {
        return count($travelers[$type->value] ?? []);
    }

    /**
     * Count all travelers (adults + children) from a raw travelers array.
     *
     * @param  array  $travelers  Travelers keyed by type value.
     */
    public function getTotalTravellers(array $travelers): int
    {
        return $this->getTotalByType($travelers, TravelerType::Adult)
            + $this->getTotalByType($travelers, TravelerType::Child);
    }
}
