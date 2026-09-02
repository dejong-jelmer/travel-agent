<?php

return [
    // Page titles
    'title_index' => 'Trips',
    'title_create' => 'New trip',
    'title_show' => 'Trip details',
    'title_edit' => 'Edit trip',

    // Flash messages
    'created' => 'Trip has been created',
    'updated' => 'Trip has been updated',
    'deleted' => 'Trip has been deleted',

    // Traveler
    'traveler' => [
        'adult' => 'Adult',
        'child' => 'Child',
    ],

    // Items
    'item' => [
        'type' => [
            'inclusion' => 'Included',
            'exclusion' => 'Excluded',
            'optional' => 'Optioneel',
        ],
        'inclusion' => [
            'itinerary' => 'Well-planned itinerary',
            'background_info' => 'Carefully compiled information package with background information and a practical guide to the destination',
            'train_reservations' => 'Seat reservation on the train (when possible)',
        ],
        'exclusion' => [
            'additional_meals' => 'Additional meals',
            'excursions' => 'Additional activities & excursions',
            'transfers' => 'Local transportation',
            'personal_expenses' => 'Personal expenses',
            'travel_cancellation_insurance' => 'Travel and/or cancellation insurance',
            'local_tourist_tax' => 'Local tourist tax',
        ],
    ],

    // Forms
    'forms' => [
        'tabs' => [
            'items' => 'Items',
        ],
        'sections' => [
            'items' => [
                'inclusions_title' => 'What\'s Included',
                'exclusions_title' => 'What\'s Not Included',
            ],
        ],
        'fields' => [
            'trip_items' => [
                'placeholder' => 'Enter item description',
            ],
        ],
    ],
    'practical-info' => [
        'sections' => [
            'travel_period' => 'Travel period',
            'departure_dates' => 'Departure dates',
            'outbound_return' => 'Outbound and return',
            'transport' => 'Transport',
            'accommodation' => 'Accommodation',
            'additional' => 'Additional',
        ],
    ],

    // Transport
    'transport' => [
        'train' => 'Train',
        'ferry' => 'Ferry',
        'bus' => 'Bus',
        'taxi' => 'Taxi',
        'transfer' => 'Transfer',
        'airplane' => 'Airplane',
    ],
];
