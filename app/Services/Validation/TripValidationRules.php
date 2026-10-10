<?php

namespace App\Services\Validation;

use App\Enums\Transport;
use App\Enums\Trip\HeroFocus;
use App\Enums\Trip\HighlightCategory;
use App\Enums\Trip\ItemType;
use App\Enums\Trip\KeyFactIcon;
use App\Enums\Trip\PriceLabel;
use App\Models\Trip;
use App\Rules\NoOverlappingPricePeriods;
use App\Services\TripContentParser;
use Illuminate\Validation\Rule;

class TripValidationRules
{
    public static function basic(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'highlights' => ['nullable', 'array'],
            'highlights.*.title' => ['nullable', 'string', 'max:255', 'distinct:ignore_case', 'required_with:highlights.*.description,highlights.*.label'],
            'highlights.*.description' => ['nullable', 'string', 'max:500'],
            // An own label replaces the default label of the category, so without a category it is never shown
            'highlights.*.category' => ['nullable', 'string', Rule::enum(HighlightCategory::class), 'required_with:highlights.*.label'],
            'highlights.*.label' => ['nullable', 'string', 'max:'.Trip::MAX_HIGHLIGHT_LABEL_LENGTH],
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }

    /**
     * The journey section is empty or the key of a section in the submitted description. The first section opens
     * the trip page in the top card and cannot be chosen.
     */
    public static function journeySection(mixed $description): array
    {
        $keys = array_column(
            app(TripContentParser::class)->storySections(is_string($description) ? $description : null),
            'key'
        );

        return [
            'journey_section' => ['nullable', 'string', Rule::in($keys)],
        ];
    }

    /**
     * Every section photo is one of the gallery images of the trip. The request already drops the keys that are not
     * a section of the submitted description.
     */
    public static function sectionImages(Trip $trip): array
    {
        return [
            'section_images' => ['array'],
            'section_images.*' => ['integer', Rule::in($trip->images()->pluck('id')->all())],
        ];
    }

    public static function keyFacts(): array
    {
        return [
            'key_facts' => ['nullable', 'array', 'max:'.Trip::MAX_KEY_FACTS],
            'key_facts.*.label' => ['required', 'string', 'max:'.Trip::MAX_KEY_FACT_LABEL_LENGTH],
            'key_facts.*.value' => ['required', 'string', 'max:'.Trip::MAX_KEY_FACT_VALUE_LENGTH],
            'key_facts.*.icon' => ['required', 'string', Rule::enum(KeyFactIcon::class)],
        ];
    }

    public static function prices(): array
    {
        return [
            'prices' => ['nullable', 'array', new NoOverlappingPricePeriods],
            'prices.*.base_price_pp' => ['required', 'numeric', 'min:0'],
            'prices.*.single_supplement' => ['required', 'numeric', 'min:0'],
            'prices.*.valid_from' => ['required', 'date'],
            'prices.*.valid_until' => ['required', 'date', 'after_or_equal:prices.*.valid_from'],
            'prices.*.label' => ['required', 'string', Rule::enum(PriceLabel::class)],
        ];
    }

    public static function settings(): array
    {
        return [
            'active' => ['boolean'],
            'featured' => ['boolean'],
            'published_at' => ['required', 'date'],
            'min_advance_days' => ['nullable', 'integer', 'min:0', 'max:730'],
        ];
    }

    public static function seo(): array
    {
        return [
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    public static function destinations(): array
    {
        return [
            'destinations' => ['required', 'array'],
        ];
    }

    public static function transport(): array
    {
        return [
            'transport' => ['array'],
            'transport.*' => [
                'required',
                'string',
                Rule::enum(Transport::class),
            ],
        ];
    }

    public static function heroImageStore(): array
    {

        return [
            'heroImage' => ['required', ...ImageValidationRules::baseImage()],
        ];
    }

    public static function heroImageUpdate(): array
    {
        return [
            'heroImage' => ['nullable', ...ImageValidationRules::baseImageOrString()],
        ];
    }

    /**
     * The focus point is one of the positions of the 3x3 grid in the admin form; empty keeps the hero image centred.
     */
    public static function heroFocus(): array
    {
        return [
            'hero_focus' => ['nullable', 'string', Rule::enum(HeroFocus::class)],
        ];
    }

    public static function imagesStore(): array
    {
        return [
            'images' => ['required', 'array'],
            'images.*' => ['required', ...ImageValidationRules::baseImage()],
        ];
    }

    public static function imagesUpdate(): array
    {
        return [
            'images' => ['nullable', 'array'],
            'images.*' => ImageValidationRules::baseImageOrString(),
        ];
    }

    public static function items(): array
    {
        return [
            'items' => ['nullable', 'array'],
            'items.*.type' => ['required', 'string', Rule::enum(ItemType::class)],
            'items.*.item' => ['required', 'string', 'max:255'],
        ];
    }

    public static function practicalInfo(): array
    {
        return [
            'practical_info' => ['nullable', 'array'],
            'practical_info.*' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public static function blockedDates(): array
    {
        return [
            'blocked_dates' => ['nullable', 'array'],
            'blocked_dates.dates' => ['nullable', 'array'],
            'blocked_dates.dates.*' => Rule::anyOf([
                ['array', 'in_array_keys:start,end'],
                ['nullable', 'date', 'after_or_equal:today'],
            ]),
            'blocked_dates.dates.*.start' => ['nullable', 'date', 'after_or_equal:today'],
            'blocked_dates.dates.*.end' => ['nullable', 'date'],

            'blocked_dates.weekdays' => ['nullable', 'array'],
            'blocked_dates.weekdays.*' => ['integer', 'between:0,6'],
        ];
    }
}
