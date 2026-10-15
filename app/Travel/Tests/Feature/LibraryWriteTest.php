<?php

namespace App\Travel\Tests\Feature;

use App\Models\User;
use App\Travel\Actions\DeleteAccommodation;
use App\Travel\Actions\DeleteCity;
use App\Travel\Actions\DeleteStation;
use App\Travel\Actions\DeleteTransitLine;
use App\Travel\Actions\UpdateTransitLine;
use App\Travel\Enums\AccommodationType;
use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Models\Accommodation;
use App\Travel\Models\City;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use App\Travel\Models\Pharmacy;
use App\Travel\Models\Station;
use App\Travel\Models\Stay;
use App\Travel\Models\TransitLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LibraryWriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_a_city_is_added_updated_and_deleted(): void
    {
        $this->post(route('admin.cities.store'), ['name' => 'Bordeaux', 'country' => 'fr'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');
        $city = City::sole();
        $this->assertSame('FR', $city->country);

        $this->put(route('admin.cities.update', $city), ['name' => 'Bordeaux', 'country' => 'FR'])->assertSessionHasNoErrors();

        $this->delete(route('admin.cities.destroy', $city))->assertSessionHasNoErrors();
        $this->assertModelMissing($city);
    }

    public function test_a_city_name_is_unique_within_its_country(): void
    {
        City::factory()->create(['name' => 'Valence', 'country' => 'FR']);

        $this->post(route('admin.cities.store'), ['name' => 'Valence', 'country' => 'FR'])
            ->assertSessionHasErrors(['name' => 'Deze plaats bestaat al in dit land.']);
        $this->post(route('admin.cities.store'), ['name' => 'Valence', 'country' => 'ES'])->assertSessionHasNoErrors();
    }

    public function test_a_city_with_stations_accommodations_or_pharmacies_is_kept(): void
    {
        foreach ([Station::class, Accommodation::class, Pharmacy::class] as $model) {
            $city = $model::factory()->create()->city;

            $this->delete(route('admin.cities.destroy', $city))
                ->assertSessionHasErrors(['city' => 'Deze plaats heeft nog stations, accommodaties of apotheken. Verwijder die eerst.']);
            $this->assertModelExists($city);
        }
    }

    public function test_a_pharmacy_is_added_to_a_city_updated_and_deleted(): void
    {
        $city = City::factory()->create();

        $this->post(route('admin.cities.pharmacies.store', $city), ['name' => 'Pharmacie Bellecour', 'address' => '1 Place Bellecour'])
            ->assertSessionHasNoErrors();
        $pharmacy = $city->pharmacies()->sole();

        $this->put(route('admin.pharmacies.update', $pharmacy), ['name' => 'Pharmacie Bellecour', 'address' => null])
            ->assertSessionHasNoErrors();
        $this->assertNull($pharmacy->fresh()?->address);

        $this->delete(route('admin.pharmacies.destroy', $pharmacy))->assertSessionHasNoErrors();
        $this->assertModelMissing($pharmacy);
    }

    public function test_a_station_is_added_updated_and_deleted(): void
    {
        $city = City::factory()->create();

        $this->post(route('admin.stations.store'), [
            'city_id' => $city->id,
            'name' => 'Bordeaux Saint-Jean',
            'latitude' => '44.8249878',
            'longitude' => '-0.5602157',
        ])->assertSessionHasNoErrors();
        $station = Station::sole();
        $this->assertSame('-0.5602157', $station->longitude);

        $this->put(route('admin.stations.update', $station), ['city_id' => $city->id, 'name' => 'Gare Saint-Jean', 'latitude' => '91'])
            ->assertSessionHasErrors('latitude');
        $this->put(route('admin.stations.update', $station), ['city_id' => $city->id, 'name' => 'Gare Saint-Jean'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Gare Saint-Jean', $station->fresh()?->name);

        $this->delete(route('admin.stations.destroy', $station))->assertSessionHasNoErrors();
        $this->assertModelMissing($station);
    }

    public function test_a_station_that_a_leg_uses_is_kept(): void
    {
        $leg = Leg::factory()->transfer()->create();

        $this->delete(route('admin.stations.destroy', $leg->from_station_id))
            ->assertSessionHasErrors(['station' => 'Dit station komt voor in 1 etappe. Haal het daar eerst weg.']);

        $this->assertSame($leg->from_station_id, $leg->fresh()?->to_station_id);
    }

    public function test_a_transit_line_is_added_updated_and_deleted(): void
    {
        $this->post(route('admin.transit-lines.store'), [
            'mode' => TransitMode::Tram->value,
            'name' => 'Tram B',
            'color' => 'red',
        ])->assertSessionHasErrors(['color' => 'Geef de kleur als hexcode, zoals #e4003f.']);

        $this->post(route('admin.transit-lines.store'), [
            'mode' => TransitMode::Tram->value,
            'name' => 'Tram B',
            'color' => '#e4003f',
            'info_link' => 'https://www.infotbm.com',
        ])->assertSessionHasNoErrors();
        $line = TransitLine::sole();

        $this->put(route('admin.transit-lines.update', $line), ['mode' => TransitMode::Bus->value, 'name' => 'Bus 9'])
            ->assertSessionHasNoErrors();
        $this->assertSame(TransitMode::Bus, $line->fresh()?->mode);

        $this->delete(route('admin.transit-lines.destroy', $line))->assertSessionHasNoErrors();
        $this->assertModelMissing($line);
    }

    public function test_the_info_link_of_a_transit_line_may_hold_the_variables_of_a_leg(): void
    {
        $line = ['mode' => TransitMode::Train->value, 'name' => 'Eurostar'];
        $eurostar = 'https://www.eurostar.com/nl-nl/reisinformatie/dienstregeling/{%leg_number%}?date={%journey_date%}';

        $this->post(route('admin.transit-lines.store'), [...$line, 'info_link' => $eurostar])->assertSessionHasNoErrors();
        $this->assertSame($eurostar, TransitLine::sole()->info_link);

        $this->post(route('admin.transit-lines.store'), [...$line, 'info_link' => 'https://www.eurostar.com/{%leg_nummer%}'])
            ->assertSessionHasErrors(['info_link' => 'De informatielink kent {%leg_nummer%} niet. Gebruik {%leg_number%} of {%journey_date%}.']);
        $this->post(route('admin.transit-lines.store'), [...$line, 'info_link' => 'www.eurostar.com/{%leg_number%}'])
            ->assertSessionHasErrors(['info_link' => 'De informatielink is geen geldige url.']);
        $this->post(route('admin.transit-lines.store'), [...$line, 'info_link' => 'https://www.eurostar.com/{leg_number}'])
            ->assertSessionHasErrors(['info_link' => 'De informatielink is geen geldige url.']);
        $this->assertSame(1, TransitLine::count());
    }

    public function test_a_transit_line_that_legs_use_keeps_its_mode_and_is_kept(): void
    {
        $line = TransitLine::factory()->create(['mode' => TransitMode::Train, 'name' => 'TGV INOUI']);
        $journey = Journey::factory()->create();
        foreach (range(1, 2) as $position) {
            Leg::factory()->train()->for($journey, 'legable')->create(['position' => $position])->transitLines()->attach($line);
        }

        $this->put(route('admin.transit-lines.update', $line), ['mode' => TransitMode::Bus->value, 'name' => 'TGV INOUI'])
            ->assertSessionHasErrors(['mode' => 'Deze lijn komt voor in 2 etappes van dit vervoermiddel, dus het vervoermiddel kan niet veranderen.']);
        $this->assertSame(TransitMode::Train, $line->refresh()->mode);

        $this->put(route('admin.transit-lines.update', $line), ['mode' => TransitMode::Train->value, 'name' => 'TGV inOui'])
            ->assertSessionHasNoErrors();
        $this->assertSame('TGV inOui', $line->refresh()->name);

        $this->delete(route('admin.transit-lines.destroy', $line))
            ->assertSessionHasErrors(['transit_line' => 'Deze lijn komt voor in 2 etappes. Haal hem daar eerst weg.']);
        $this->assertModelExists($line);
    }

    public function test_an_accommodation_is_added_updated_and_deleted_with_its_arrival_route(): void
    {
        $city = City::factory()->create();
        $attributes = [
            'city_id' => $city->id,
            'name' => "Guest house L'Esprit des Chartrons",
            'type' => AccommodationType::GuestHouse->value,
            'check_in' => '16:00',
        ];

        $this->post(route('admin.accommodations.store'), [...$attributes, 'check_out' => '11 uur'])->assertSessionHasErrors('check_out');
        $this->post(route('admin.accommodations.store'), [...$attributes, 'check_out' => '11:30'])->assertSessionHasNoErrors();
        $accommodation = Accommodation::sole();
        $this->assertSame('16:00:00', $accommodation->check_in);

        $this->put(route('admin.accommodations.update', $accommodation), [...$attributes, 'type' => AccommodationType::Hotel->value])
            ->assertSessionHasNoErrors();
        $this->assertSame(AccommodationType::Hotel, $accommodation->fresh()?->type);

        $route = Leg::factory()->walk()->for($accommodation, 'legable')->create();
        $this->delete(route('admin.accommodations.destroy', $accommodation))->assertSessionHasNoErrors();
        $this->assertModelMissing($accommodation);
        $this->assertModelMissing($route);
    }

    public function test_an_accommodation_with_stays_is_kept(): void
    {
        $stay = Stay::factory()->create();

        $this->delete(route('admin.accommodations.destroy', $stay->accommodation_id))
            ->assertSessionHasErrors(['accommodation' => 'Deze accommodatie hoort bij 1 verblijf. Haal haar daar eerst weg.']);

        $this->assertModelExists($stay->accommodation);
    }

    /**
     * A race cannot be played out in one process, so this checks what the guarantee rests on: each delete locks its
     * row before it counts what uses it, so a leg or stay that would take the row waits for the delete.
     */
    public function test_each_delete_locks_its_row_before_it_checks_what_uses_it(): void
    {
        DB::enableQueryLog();

        foreach ([
            [DeleteStation::class, Station::factory()->create(), 'travel_stations'],
            [DeleteTransitLine::class, TransitLine::factory()->create(), 'travel_transit_lines'],
            [DeleteAccommodation::class, Accommodation::factory()->create(), 'travel_accommodations'],
            [DeleteCity::class, City::factory()->create(), 'travel_cities'],
        ] as [$action, $model, $table]) {
            DB::flushQueryLog();
            app($action)->handle($model);
            $queries = array_column(DB::getQueryLog(), 'query');

            $this->assertMatchesRegularExpression("/^select .* from `{$table}` where .* for update$/", $queries[0], $action);
            $this->assertModelMissing($model);
        }
    }

    public function test_a_line_is_locked_before_its_legs_are_counted(): void
    {
        $line = TransitLine::factory()->create(['mode' => TransitMode::Train]);

        DB::enableQueryLog();
        app(UpdateTransitLine::class)->handle($line, ['mode' => TransitMode::Bus->value]);
        $queries = array_column(DB::getQueryLog(), 'query');

        $this->assertMatchesRegularExpression('/^select .* from `travel_transit_lines` where .* for update$/', $queries[0]);
        $this->assertSame(TransitMode::Bus, $line->refresh()->mode);
    }

    public function test_a_non_admin_cannot_write_to_the_library(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'guest']));

        $this->post(route('admin.cities.store'), ['name' => 'Bordeaux', 'country' => 'FR'])->assertForbidden();
        $this->post(route('admin.transit-lines.store'), ['mode' => LegType::Tram->value, 'name' => 'Tram B'])->assertForbidden();

        $this->assertDatabaseCount('travel_cities', 0);
        $this->assertDatabaseCount('travel_transit_lines', 0);
    }
}
