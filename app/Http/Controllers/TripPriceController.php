<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Exceptions\NoPriceAvailableException;
use App\Models\Trip;
use App\Services\PriceCalculatorService;
use App\Support\MoneyHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TripPriceController extends Controller
{
    public function __construct(private PriceCalculatorService $priceCalculator) {}

    /**
     * Calculate and return prices for a trip based on travelers and date.
     *
     * @param  Trip  $trip  The trip to calculate prices for
     */
    public function __invoke(Trip $trip): JsonResponse
    {
        $validated = request()->validate([
            'travelers' => ['required', 'integer', 'min:1', 'max:6'],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $persons = (int) $validated['travelers'];
        $date = Carbon::createFromFormat('Y-m-d', $validated['date']);

        try {
            $prices = $this->priceCalculator->forTrip($trip, $persons, $date);
        } catch (NoPriceAvailableException $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'No prices available'], 404);
        }

        return response()->json([
            'price_per_person' => $this->formatAmount($prices->perPerson),
            'total_price' => $this->formatAmount($prices->baseTotal),
            'single_supplement' => $this->formatAmount($prices->singleSupplement),
            'booking_fee' => $this->formatAmount($prices->feesAndFunds[SettingKey::BookingFee->value]),
            'guarantee_fund' => $this->formatAmount($prices->feesAndFunds[SettingKey::GuaranteeFund->value]),
            'emergency_fund' => $this->formatAmount($prices->feesAndFunds[SettingKey::EmergencyFund->value]),
            'grand_total' => $this->formatAmount($prices->grandTotal),
        ]);
    }

    private function formatAmount($money): float
    {
        return $money->getAmount() / MoneyHelper::CENTS_PER_UNIT;
    }
}
