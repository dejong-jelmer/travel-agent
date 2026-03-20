<?php

namespace Tests\Unit\Enums;

use App\Enums\TravelerType;
use PHPUnit\Framework\TestCase;

class TravelerTypeTest extends TestCase
{
    public function test_from_key_throws_exception_for_unknown_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown traveler type: unknown');

        TravelerType::fromKey('unknown');
    }

    public function test_from_key_handles_case_insensitive_input(): void
    {
        $this->assertSame(TravelerType::Adult, TravelerType::fromKey('ADULT'));
        $this->assertSame(TravelerType::Adult, TravelerType::fromKey('Adult'));
        $this->assertSame(TravelerType::Child, TravelerType::fromKey('CHILD'));
        $this->assertSame(TravelerType::Child, TravelerType::fromKey('Child'));
    }

    public function test_relation_name_returns_children_for_child_type(): void
    {
        $this->assertSame('children', TravelerType::Child->relationName());
    }

    public function test_relation_name_returns_pluralized_value_for_adult(): void
    {
        $this->assertSame('adults', TravelerType::Adult->relationName());
    }

    public function test_values_returns_all_enum_values(): void
    {
        $values = TravelerType::values();

        $this->assertIsArray($values);
        $this->assertContains('adult', $values);
        $this->assertContains('child', $values);
        $this->assertCount(2, $values);
    }
}
