<?php

namespace App\Services;

use App\DTO\BookingData;
use App\Enums\TravelerType;
use App\Models\Booking;
use App\Models\BookingChange;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    public function store(BookingData $bookingData): Booking
    {
        // Get Data from DTO
        $contact = $bookingData->contact->toArray();
        $adults = $bookingData->adult;
        $children = $bookingData->child;

        // Create booking
        $booking = $bookingData->trip->bookings()->create([
            'departure_date' => $bookingData->date,
            'confirmed' => $bookingData->confirmed,
        ]);

        // Create a booking regerence
        $year = now()->format('Y');
        $booking->reference = "{$year}-".str_pad($booking->id, 6, '0', STR_PAD_LEFT);
        $booking->save();

        // Create booking contact details
        $booking->contact()->create($contact);

        // Create the travelers
        $this->createTraveler($booking, $adults, 'adult', $bookingData->main_booker);
        $this->createTraveler($booking, $children, 'child');

        return $booking;
    }

    public function update(Booking $booking, BookingData $bookingData): Booking
    {
        // Get Data from DTO
        $contact = $bookingData->contact->toArray();
        $adults = $bookingData->adult;
        $children = $bookingData->child;

        // Update booking contact details and log the changes
        $this->updateChangeLog($booking->id, $booking->contact, $contact, 'contact');
        $booking->contact()->update($contact);

        // Update booking traveler details and log the changes
        $this->updateTraveler($booking, $adults, 'adults', $bookingData->main_booker);
        $this->updateTraveler($booking, $children, 'children');

        return $booking;
    }

    public function seen(Booking $booking): void
    {
        $booking->timestamps = false;
        $booking->new = false;
        $booking->save();
    }

    private function createTraveler(Booking $booking, array $travelers, string $type, ?int $mainBookerIndex = null)
    {
        foreach ($travelers as $index => $traveler) {
            $travelerModel = $booking->travelers()->create([
                'type' => TravelerType::fromKey($type),
                ...$traveler,
            ]);

            if ($mainBookerIndex !== null && $mainBookerIndex === $index && $type === 'adult') {
                $this->updateMainBooker($booking, $travelerModel->id);
            }
        }
    }

    private function updateTraveler(Booking $booking, array $travelers, string $type, ?int $mainBookerIndex = null)
    {
        foreach ($travelers as $index => $traveler) {
            $this->updateChangeLog($booking->id, $booking->travelers->find($traveler['id']), $traveler, "{$type}.{$index}");
            $travelerModel = $booking->travelers()->find($traveler['id']);
            $travelerModel->update(Arr::except($traveler, ['full_name']));
            if ($mainBookerIndex !== null && $mainBookerIndex === $index) {
                $this->updateMainBooker($booking, $travelerModel->id);
            }
        }
    }

    private function updateMainBooker(Booking $booking, $id): void
    {
        $this->updateChangeLog($booking->id, $booking, ['main_booker_id' => $id]);
        $booking->update([
            'main_booker_id' => $id,
        ]);
    }

    public function updateChangeLog(int $id, Model $model, array $data, ?string $for = null): void
    {
        foreach ($data as $field => $value) {
            $old = data_get($model, $field);
            if ($old != $value) {
                $logField = $for ? "{$for}." : '';
                BookingChange::create([
                    'booking_id' => $id,
                    'admin_id' => Auth::id(),
                    'field' => $logField.$field,
                    'old_value' => $old,
                    'new_value' => $value,
                ]);
            }
        }
    }
}
