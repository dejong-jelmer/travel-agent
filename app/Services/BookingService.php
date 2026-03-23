<?php

namespace App\Services;

use App\DTO\CreateBookingData;
use App\DTO\TripPriceData;
use App\DTO\UpdateBookingData;
use App\Enums\SettingKey;
use App\Enums\TravelerType;
use App\Models\Booking;

class BookingService
{
    /**
     * Create a new booking with contact details and travelers from validated DTO data.
     *
     * @param  CreateBookingData  $bookingData  Validated and typed booking input data.
     * @param  TripPriceData  $prices  Calculated price breakdown for the trip and date.
     * @return Booking The newly created booking model.
     */
    public function create(CreateBookingData $bookingData, TripPriceData $prices): Booking
    {
        // Get Data from DTO
        $contactData = $bookingData->contact->toArray();
        $travelersData = $bookingData->travelers;

        $totalAdults = $this->getTotalByType($bookingData->travelers, TravelerType::Adult);
        $totalChildren = $this->getTotalByType($bookingData->travelers, TravelerType::Child);

        // Create booking
        /** @var Booking $booking */
        $booking = $bookingData->trip->bookings()->create([
            'departure_date' => $bookingData->date,
            'has_accepted_conditions' => $bookingData->has_accepted_conditions,
            'has_confirmed' => $bookingData->has_confirmed,
            'total_adults' => $totalAdults,
            'total_children' => $totalChildren,
            'trip_price_id' => $prices->tripPriceId,
            'price_per_person' => $prices->perPerson->getAmount(),
            'single_supplement' => $prices->singleSupplement->getAmount(),
            'base_total_price' => $prices->baseTotal->getAmount(),
            'grand_total_price' => $prices->baseTotal->getAmount(),
            'fees_and_funds' => [
                SettingKey::BookingFee->value => $prices->feesAndFunds[SettingKey::BookingFee->value]->getAmount(),
                SettingKey::EmergencyFund->value => $prices->feesAndFunds[SettingKey::EmergencyFund->value]->getAmount(),
                SettingKey::GuaranteeFund->value => $prices->feesAndFunds[SettingKey::GuaranteeFund->value]->getAmount(),
            ],
        ]);

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
        $this->storeTravelers($booking, $travelersData, $bookingData->main_booker['index']);

        return $booking;
    }

    /**
     * Update an existing booking's status, contact details, and travelers.
     *
     * @param  Booking  $booking  The booking to update.
     * @param  UpdateBookingData  $bookingData  Validated and typed update input data.
     * @return Booking The updated booking model.
     */
    public function update(Booking $booking, UpdateBookingData $bookingData): Booking
    {
        // Update booking
        $booking->update([
            'status' => $bookingData->status,
            'payment_status' => $bookingData->payment_status,
            'internal_notes' => $bookingData->internal_notes,
        ]);
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

        $this->updateTravelers($booking, $travelersData, $bookingData->main_booker['index']);

        return $booking;
    }

    /**
     * Persist all travelers for a new booking and assign the main booker.
     *
     * @param  Booking  $booking  The booking to attach travelers to.
     * @param  array  $data  Travelers keyed by type (adults/children), each an array of traveler fields.
     * @param  int  $mainBookerIndex  Zero-based index of the adult traveler designated as main booker.
     */
    private function storeTravelers(Booking $booking, array $data, int $mainBookerIndex): void
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
    private function updateTravelers(Booking $booking, array $data, int $mainBookerIndex): void
    {
        foreach ($data as $travelers) {
            foreach ($travelers as $index => $travelerData) {
                /** @var \App\Models\BookingTraveler $travelerModel */
                $travelerModel = $booking->travelers()->updateOrCreate(
                    ['id' => $travelerData['id']],
                    [
                        'first_name' => $travelerData['first_name'],
                        'last_name' => $travelerData['last_name'],
                        'birthdate' => $travelerData['birthdate'],
                        'nationality' => $travelerData['nationality'],
                        'special_requests' => $travelerData['special_requests'] ?? null,
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
