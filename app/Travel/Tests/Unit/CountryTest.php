<?php

namespace App\Travel\Tests\Unit;

use App\Travel\Documents\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function test_france_has_its_ambulance_number_and_drinking_water_signs(): void
    {
        $france = Country::for('fr');

        $this->assertSame('FR', $france->code);
        $this->assertSame('Frankrijk', $france->name);
        $this->assertSame('112 of 15', $france->emergencyNumbers);
        $this->assertTrue($france->idCardSuffices);
        $this->assertTrue($france->ehic);
        $this->assertSame(['eau potable', 'eau non potable'], $france->drinkingWater);
    }

    public function test_the_united_kingdom_takes_a_passport_but_the_ehic(): void
    {
        $kingdom = Country::for('GB');

        $this->assertSame('999 of 112', $kingdom->emergencyNumbers);
        $this->assertFalse($kingdom->idCardSuffices);
        $this->assertTrue($kingdom->ehic);
        $this->assertNull($kingdom->drinkingWater);
    }

    public function test_what_is_not_known_of_a_country_falls_back_to_what_holds_across_europe(): void
    {
        $morocco = Country::for('MA');

        $this->assertSame('112', $morocco->emergencyNumbers);
        $this->assertFalse($morocco->idCardSuffices);
        $this->assertFalse($morocco->ehic);
        $this->assertNull($morocco->drinkingWater);
    }
}
