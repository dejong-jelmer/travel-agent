<?php

use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\ItemType;

return [
    ItemType::Inclusion->value => [
        ItemCategory::GeneralInclusions->value => [
            'trip.item.general_inclusions.itinerary',
            'trip.item.general_inclusions.background_info',
            'trip.item.general_inclusions.train_reservations',
        ],
    ],
    ItemType::Exclusion->value => [
        ItemCategory::CostsToConsider->value => [
            'trip.item.costs_to_consider.additional_meals',
            'trip.item.costs_to_consider.excursions',
            'trip.item.costs_to_consider.transfers',
            'trip.item.costs_to_consider.personal_expenses',
            'trip.item.costs_to_consider.travel_cancellation_insurance',
            'trip.item.costs_to_consider.local_tourist_tax',
        ],
    ],
];
