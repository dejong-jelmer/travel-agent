<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Exceptions\NoPriceAvailableException;
use App\Models\Trip;
use App\Services\PriceCalculatorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TripPriceController extends Controller
{
    public function __construct(private PriceCalculatorService $priceCalculator) {}

    public function getPrices(Trip $trip)
    {
        $prices = null;
        $persons = request()->input('travelers');
        $date = Carbon::parse(request()->input('date'));

        try {
            $prices = $this->priceCalculator->forTrip($trip, $persons, $date);
        } catch (NoPriceAvailableException $e) {
            Log::error($e->getMessage());
        }

        return response()->json([
            'price_per_person' => $prices ? $prices->perPerson->getAmount() / 100 : 0,
            'total_price' => $prices ? $prices->baseTotal->getAmount() / 100 : 0,
            'single_supplement' => $prices ? $prices->singleSupplement->getAmount() / 100 : 0,
            'booking_fee' => $prices ? $prices->feesAndFunds[SettingKey::BookingFee->value]->getAmount() / 100 : 0,
            'guarantee_fund' => $prices ? $prices->feesAndFunds[SettingKey::GuaranteeFund->value]->getAmount() / 100 : 0,
            'emergency_fund' => $prices ? $prices->feesAndFunds[SettingKey::EmergencyFund->value]->getAmount() / 100 : 0,
            'grand_total' => $prices ? $prices->grandTotal->getAmount() / 100 : 0,
        ], 200);
    }
}
