<?php

namespace Tests\Unit\Enums;

use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\ItemType;
use PHPUnit\Framework\TestCase;

class ItemCategoryTest extends TestCase
{
    public function test_type_returns_correct_item_type_for_each_category(): void
    {
        $this->assertSame(ItemType::Inclusion, ItemCategory::GeneralInclusions->type());
        $this->assertSame(ItemType::Inclusion, ItemCategory::Transport->type());
        $this->assertSame(ItemType::Inclusion, ItemCategory::Accommodation->type());
        $this->assertSame(ItemType::Exclusion, ItemCategory::AdditionalCost->type());
        $this->assertSame(ItemType::Exclusion, ItemCategory::CostsToConsider->type());
    }

    public function test_is_customizable_returns_false_for_fixed_categories(): void
    {
        $this->assertFalse(ItemCategory::GeneralInclusions->isCustomizable());
        $this->assertFalse(ItemCategory::CostsToConsider->isCustomizable());
        $this->assertTrue(ItemCategory::Transport->isCustomizable());
        $this->assertTrue(ItemCategory::Accommodation->isCustomizable());
        $this->assertTrue(ItemCategory::AdditionalCost->isCustomizable());
    }

    public function test_extra_options_returns_disabled_and_type(): void
    {
        foreach (ItemCategory::cases() as $category) {
            $options = $category->extraOptions();

            $this->assertArrayHasKey('disabled', $options);
            $this->assertArrayHasKey('type', $options);
            $this->assertSame(! $category->isCustomizable(), $options['disabled']);
            $this->assertSame($category->type(), $options['type']);
        }
    }
}
