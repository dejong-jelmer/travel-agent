<?php

namespace App\Services;

use App\Exceptions\NoPriceAvailableException;
use App\Models\Departure;
use App\Models\Trip;

/**
 * The departures of a group trip as the public site shows them.
 *
 * A visitor sees what helps to decide and what has to be told before booking: the dates, the price, whether the
 * departure is certain, and otherwise from how many participants it goes ahead and by when that is known. How many
 * people have booked is not shown, only that few places are left.
 */
class DepartureListing
{
    public const AVAILABLE = 'available';

    public const FEW_SEATS = 'few_seats';

    public const FULL = 'full';

    public function __construct(
        private PriceCalculatorService $priceCalculator,
        private FeesAndFundsService $feesAndFunds,
    ) {}

    /**
     * The departures a visitor can still request, the first to leave first. Empty for an individual trip.
     *
     * @return list<array{
     *     id: int,
     *     departs_on: string,
     *     returns_on: string,
     *     duration_in_days: int,
     *     price_per_person: int|null,
     *     single_supplement: int|null,
     *     availability: string,
     *     seats_left: int|null,
     *     is_guaranteed: bool,
     *     min_participants: int|null,
     *     decision_date: string|null,
     *     requestable_until: string,
     * }>
     */
    public function forTrip(Trip $trip): array
    {
        $listing = [];

        foreach ($trip->requestableDepartures() as $departure) {
            $listing[] = $this->forDeparture($departure->setRelation('trip', $trip), $trip->min_advance_days);
        }

        return $listing;
    }

    /**
     * What a booking pays on top of the price per person, once per booking, in cents: the amounts that are set, and
     * their total.
     *
     * @return array{total: int, items: array<string, int>}
     */
    public function feesPerBooking(): array
    {
        $items = array_filter($this->feesAndFunds->asCents(), fn (int $cents) => $cents > 0);

        return ['total' => array_sum($items), 'items' => $items];
    }

    /**
     * @return array<string, mixed>
     */
    private function forDeparture(Departure $departure, ?int $advanceDays): array
    {
        try {
            $price = $this->priceCalculator->forDeparture($departure, persons: 1, singleRooms: 1);
        } catch (NoPriceAvailableException) {
            $price = null;
        }

        $seatsLeft = $departure->availableSeats();
        $isGuaranteed = $departure->isGuaranteed();

        return [
            'id' => $departure->id,
            'departs_on' => $departure->departs_on->toDateString(),
            'returns_on' => $departure->returns_on->toDateString(),
            'duration_in_days' => $departure->durationInDays(),
            // In cents: the departure's own price, or the price of the trip for the day of departure
            'price_per_person' => $price ? (int) $price->perPerson->getAmount() : null,
            'single_supplement' => $price ? (int) $price->singleSupplement->getAmount() : null,
            'availability' => match (true) {
                $seatsLeft === 0 => self::FULL,
                $seatsLeft <= config('departures.few_seats_threshold') => self::FEW_SEATS,
                default => self::AVAILABLE,
            },
            // Only named when few are left
            'seats_left' => $seatsLeft > 0 && $seatsLeft <= config('departures.few_seats_threshold') ? $seatsLeft : null,
            'is_guaranteed' => $isGuaranteed,
            // The condition it goes ahead on, while it is not certain yet
            'min_participants' => $isGuaranteed ? null : $departure->min_participants,
            // The day the visitor hears whether it goes ahead; a day that has passed promises nothing anymore
            'decision_date' => $isGuaranteed || $departure->decision_date->lt(today())
                ? null
                : $departure->decision_date->toDateString(),
            // The last day a place can be requested, for the structured data
            'requestable_until' => $departure->lastRequestDate($advanceDays)->toDateString(),
        ];
    }
}
