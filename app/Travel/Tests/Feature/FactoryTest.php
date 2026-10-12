<?php

namespace App\Travel\Tests\Feature;

use App\Travel\Models\Accommodation;
use App\Travel\Models\City;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use App\Travel\Models\Pharmacy;
use App\Travel\Models\Station;
use App\Travel\Models\Stay;
use App\Travel\Models\TransitLine;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  Closure(): Factory<Model>  $factory
     */
    #[DataProvider('factories')]
    public function test_the_factory_creates_a_valid_model(Closure $factory): void
    {
        $this->assertModelExists($factory()->create());
    }

    /**
     * @return array<string, array{Closure(): Factory<Model>}>
     */
    public static function factories(): array
    {
        return [
            'city' => [fn () => City::factory()],
            'station' => [fn () => Station::factory()],
            'transit line' => [fn () => TransitLine::factory()],
            'accommodation' => [fn () => Accommodation::factory()],
            'pharmacy' => [fn () => Pharmacy::factory()],
            'journey' => [fn () => Journey::factory()],
            'stay' => [fn () => Stay::factory()],
            'leg' => [fn () => Leg::factory()],
            'train leg' => [fn () => Leg::factory()->train()],
            'transfer leg' => [fn () => Leg::factory()->transfer()],
            'walk leg' => [fn () => Leg::factory()->walk()],
            'arrival route leg' => [fn () => Leg::factory()->walk()->accommodationRoute()],
        ];
    }

    public function test_a_leg_can_belong_to_the_arrival_route_of_an_accommodation(): void
    {
        $accommodation = Accommodation::factory()->create();

        $leg = Leg::factory()->for($accommodation, 'legable')->create();

        $this->assertTrue($leg->legable->is($accommodation));
        $this->assertDatabaseCount('travel_journeys', 0);
    }
}
