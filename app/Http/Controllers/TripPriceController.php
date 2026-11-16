<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Exceptions\NoPriceAvailableException;
use App\Models\Departure;
use App\Models\Trip;
use App\Services\PriceCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TripPriceController extends Controller
{
    public function __construct(private PriceCalculatorService $priceCalculator) {}

    /**
     * Calculate and return prices for a trip based on travelers and date, or, for a group trip, based on travelers,
     * single rooms and the departure.
     *
     * @param  Trip  $trip  The trip to calculate prices for
     */
    public function __invoke(Trip $trip): JsonResponse
    {
        $validated = request()->validate([
            'travelers' => ['required', 'integer', 'min:1', 'max:6'],
            'date' => ['required_without:departure_id', 'date_format:Y-m-d'],
            'departure_id' => ['nullable', 'integer', Rule::exists(Departure::class, 'id')->where('trip_id', $trip->id)],
            'single_rooms' => ['nullable', 'integer', 'min:0', 'lte:travelers'],
        ]);

        $persons = (int) $validated['travelers'];

        try {
            $prices = isset($validated['departure_id'])
                ? $this->priceCalculator->forDeparture(
                    Departure::findOrFail($validated['departure_id']),
                    $persons,
                    (int) ($validated['single_rooms'] ?? 0)
                )
                : $this->priceCalculator->forTrip($trip, $persons, Carbon::createFromFormat('Y-m-d', $validated['date']));
        } catch (NoPriceAvailableException $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'No prices available'], 404);
        }

        return response()->json([
            'price_per_person' => $this->priceCalculator->formatAmount($prices->perPerson),
            'total_price' => $this->priceCalculator->formatAmount($prices->baseTotal),
            'single_supplement' => $this->priceCalculator->formatAmount($prices->singleSupplement),
            'booking_fee' => $this->priceCalculator->formatAmount($prices->feesAndFunds[SettingKey::BookingFee->value]),
            'guarantee_fund' => $this->priceCalculator->formatAmount($prices->feesAndFunds[SettingKey::GuaranteeFund->value]),
            'emergency_fund' => $this->priceCalculator->formatAmount($prices->feesAndFunds[SettingKey::EmergencyFund->value]),
            'grand_total' => $this->priceCalculator->formatAmount($prices->grandTotal),
        ]);
    }
}
