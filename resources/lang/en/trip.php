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
        'category' => [
            'general_inclusions' => 'Always included in our trips',
            'transport' => 'Transport',
            'accommodation' => 'Accommodation',
            'additional_cost' => 'Additional costs',
            'costs_to_consider' => 'Costs to consider',
        ],
        'type' => [
            'inclusion' => 'Included',
            'exclusion' => 'Excluded',
        ],
        'general_inclusions' => [
            'itinerary' => 'Well-planned itinerary',
            'accommodation_breakfast' => 'Accommodation always includes breakfast',
            'train_reservations' => 'Seat reservation on the train (when possible)',
        ],
        'additional_cost' => [
            'fees' => [
                'booking' => 'Booking fee - €27.50 per booking',
                'guarantee_fund' => 'STO guarantee fund - €10 per booking',
                'emergency_fund' => 'Emergency fund - €2.50 per booking',
            ],
        ],
        'costs_to_consider' => [
            'additional_meals' => 'Additional meals',
            'activities' => 'Activities',
            'excursions' => 'Excursions',
            'tips' => 'Tips',
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
