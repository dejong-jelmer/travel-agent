<?php

use App\Enums\Trip\ItemType;

return [
    ItemType::Inclusion->value => [
        'trip.item.inclusion.itinerary',
        // 'trip.item.inclusion.background_info',
        'trip.item.inclusion.train_reservations',
    ],
    ItemType::Exclusion->value => [
        'trip.item.exclusion.additional_meals',
        'trip.item.exclusion.excursions',
        'trip.item.exclusion.transfers',
        'trip.item.exclusion.personal_expenses',
        'trip.item.exclusion.travel_cancellation_insurance',
        'trip.item.exclusion.local_tourist_tax',
    ],
];
