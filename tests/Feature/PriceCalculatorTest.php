<?php

namespace Tests\Feature;

use App\Enums\SettingKey;
use App\Enums\Trip\TripFormat;
use App\Exceptions\NoPriceAvailableException;
use App\Models\Departure;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\TripPrice;
use App\Services\PriceCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PriceCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private PriceCalculatorService $calculator;

    private Trip $trip;

    private TripPrice $tripPrice;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set(SettingKey::BookingFee, '25.00');
        Setting::set(SettingKey::GuaranteeFund, '10.00');
        Setting::set(SettingKey::EmergencyFund, '2.50');

        $this->calculator = app(PriceCalculatorService::class);
        $this->trip = Trip::factory()->create(['format' => TripFormat::Group]);
        $this->tripPrice = TripPrice::factory()->for($this->trip)->create([
            'valid_from' => '2027-01-01',
            'valid_until' => '2027-12-31',
            'base_price_pp' => 100000,
            'single_supplement' => 20000,
        ]);
    }

    private function departure(array $attributes = []): Departure
    {
        return Departure::factory()->for($this->trip)->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-27',
            'price_per_person' => 120000,
            'single_supplement' => 15000,
            ...$attributes,
        ]);
    }

    // Individual trips

    public function test_trip_price_charges_the_single_supplement_for_one_traveler_only(): void
    {
        $solo = $this->calculator->forTrip($this->trip, 1, Carbon::parse('2027-06-20'));
        $couple = $this->calculator->forTrip($this->trip, 2, Carbon::parse('2027-06-20'));

        $this->assertSame('20000', $solo->singleSupplement->getAmount());
        $this->assertSame('123750', $solo->grandTotal->getAmount());
        $this->assertSame('0', $couple->singleSupplement->getAmount());
        $this->assertSame('200000', $couple->baseTotal->getAmount());
        $this->assertSame('203750', $couple->grandTotal->getAmount());
        $this->assertSame($this->tripPrice->id, $couple->tripPriceId);
    }

    // Departures

    public function test_departure_price_uses_the_price_of_the_departure(): void
    {
        $price = $this->calculator->forDeparture($this->departure(), 3);

        $this->assertSame('120000', $price->perPerson->getAmount());
        $this->assertSame('360000', $price->baseTotal->getAmount());
        $this->assertSame('0', $price->singleSupplement->getAmount());
        $this->assertSame('15000', $price->singleSupplementPerRoom->getAmount());
        $this->assertSame('363750', $price->grandTotal->getAmount());
        $this->assertNull($price->tripPriceId);
    }

    public function test_departure_price_charges_the_supplement_for_every_single_room(): void
    {
        $price = $this->calculator->forDeparture($this->departure(), 3, singleRooms: 2);

        $this->assertSame('30000', $price->singleSupplement->getAmount());
        $this->assertSame('393750', $price->grandTotal->getAmount());
    }

    public function test_one_traveler_does_not_pay_the_supplement_without_a_single_room(): void
    {
        $price = $this->calculator->forDeparture($this->departure(), 1, singleRooms: 0);

        $this->assertSame('0', $price->singleSupplement->getAmount());
        $this->assertSame('123750', $price->grandTotal->getAmount());
    }

    public function test_departure_without_prices_falls_back_on_the_price_of_the_trip(): void
    {
        $departure = $this->departure(['price_per_person' => null, 'single_supplement' => null]);

        $price = $this->calculator->forDeparture($departure, 2, singleRooms: 1);

        $this->assertSame('100000', $price->perPerson->getAmount());
        $this->assertSame('20000', $price->singleSupplement->getAmount());
        $this->assertSame('223750', $price->grandTotal->getAmount());
        $this->assertSame($this->tripPrice->id, $price->tripPriceId);
    }

    public function test_departure_falls_back_per_amount(): void
    {
        $ownPrice = $this->calculator->forDeparture($this->departure(['single_supplement' => null]), 1, singleRooms: 1);
        $ownSupplement = $this->calculator->forDeparture($this->departure(['price_per_person' => null]), 1, singleRooms: 1);

        $this->assertSame('120000', $ownPrice->perPerson->getAmount());
        $this->assertSame('20000', $ownPrice->singleSupplement->getAmount());
        $this->assertNull($ownPrice->tripPriceId);

        $this->assertSame('100000', $ownSupplement->perPerson->getAmount());
        $this->assertSame('15000', $ownSupplement->singleSupplement->getAmount());
        $this->assertSame($this->tripPrice->id, $ownSupplement->tripPriceId);
    }

    public function test_supplement_of_zero_on_the_departure_means_no_supplement(): void
    {
        $price = $this->calculator->forDeparture($this->departure(['single_supplement' => 0]), 1, singleRooms: 1);

        $this->assertSame('0', $price->singleSupplement->getAmount());
    }

    public function test_departure_with_its_own_price_needs_no_price_on_the_trip(): void
    {
        $this->tripPrice->delete();

        $price = $this->calculator->forDeparture($this->departure(['single_supplement' => null]), 2, singleRooms: 1);

        $this->assertSame('240000', $price->baseTotal->getAmount());
        $this->assertSame('0', $price->singleSupplement->getAmount());
    }

    public function test_departure_without_any_price_per_person_has_no_price(): void
    {
        $departure = $this->departure([
            'departs_on' => '2028-06-20',
            'returns_on' => '2028-06-27',
            'price_per_person' => null,
        ]);

        $this->expectException(NoPriceAvailableException::class);

        $this->calculator->forDeparture($departure, 2);
    }
}
