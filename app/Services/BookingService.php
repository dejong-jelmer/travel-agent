<?php

namespace App\Services;

use App\DTO\CreateBookingData;
use App\DTO\UpdateBookingData;
use App\Enums\TravelerType;
use App\Models\Booking;

class BookingService
{
    public function create(CreateBookingData $bookingData): Booking
    {
        // Get Data from DTO
        $contactData = $bookingData->contact->toArray();
        $travelersData = $bookingData->travelers;

        // Create booking
        /** @var Booking $booking */
        $booking = $bookingData->trip->bookings()->create([
            'departure_date' => $bookingData->date,
            'has_accepted_conditions' => $bookingData->has_accepted_conditions,
            'has_confirmed' => $bookingData->has_confirmed,
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

    public function update(Booking $booking, UpdateBookingData $bookingData): Booking
    {
        // Update booking
        $booking->update([
            'status' => $bookingData->status,
            'payment_status' => $bookingData->payment_status,
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
                ]);
                if ($mainBookerIndex === $index && $travelerModel->type === TravelerType::Adult) {
                    $booking->main_booker_id = $travelerModel->id;
                    $booking->saveQuietly();
                }
            }
        }
    }

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
}
