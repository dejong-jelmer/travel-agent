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

    // Key fact icons
    'key_fact_icon' => [
        'train' => 'Train',
        'night' => 'Night',
        'clock' => 'Time',
        'transfer' => 'Transfer',
        'location' => 'Location',
        'bed' => 'Overnight stay',
        'breakfast' => 'Breakfast',
        'mountain' => 'Mountains',
        'calendar' => 'Calendar',
        'sun' => 'Sun',
        'info' => 'Information',
    ],

    // Hero focus points
    'hero_focus' => [
        '0% 0%' => 'Top left',
        '50% 0%' => 'Top centre',
        '100% 0%' => 'Top right',
        '0% 50%' => 'Left',
        '50% 50%' => 'Centre',
        '100% 50%' => 'Right',
        '0% 100%' => 'Bottom left',
        '50% 100%' => 'Bottom centre',
        '100% 100%' => 'Bottom right',
    ],

    // Highlight categories
    'highlight_category' => [
        'roman' => 'Roman',
        'church' => 'Church',
        'castle' => 'Castle',
        'historic_village' => 'Historic village',
        'historic_city' => 'Historic city',
        'museum' => 'Museum',
        'theater' => 'Theatre',
        'viewpoint' => 'Viewpoint',
        'square' => 'Square and market',
        'walk' => 'Walk',
        'mountain' => 'Mountains',
        'garden' => 'Garden and park',
        'water' => 'River and lake',
        'food' => 'Food',
        'wine' => 'Wine',
        'prehistory' => 'Prehistory',
        'cave' => 'Cave',
        'day_trip' => 'Day trips',
        'romantic' => 'Romantic',
        'other' => 'Sight',
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

    // How a trip travels, shown on the trip cards: by night train when its itinerary has a night train
    'travel_mode' => [
        'night_train' => 'Night train',
        'day_train' => 'Day train',
    ],
];
