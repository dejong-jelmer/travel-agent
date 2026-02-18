<?php

use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\ItemType;

return [
    ItemType::Inclusion->value => [
        ItemCategory::GeneralInclusions->value => [
            'trip.item.general_inclusions.itinerary',
            'trip.item.general_inclusions.accommodation_breakfast',
            'trip.item.general_inclusions.train_reservations',
        ],
    ],
    ItemType::Exclusion->value => [
        ItemCategory::AdditionalCost->value => [
            'trip.item.additional_cost.fees.booking',
            'trip.item.additional_cost.fees.guarantee_fund',
            'trip.item.additional_cost.fees.emergency_fund',
        ],
        ItemCategory::CostsToConsider->value => [
            'trip.item.costs_to_consider.additional_meals',
            'trip.item.costs_to_consider.activities',
            'trip.item.costs_to_consider.excursions',
            'trip.item.costs_to_consider.tips',
            'trip.item.costs_to_consider.personal_expenses',
            'trip.item.costs_to_consider.travel_cancellation_insurance',
            'trip.item.costs_to_consider.local_tourist_tax',
        ],
    ],
];
