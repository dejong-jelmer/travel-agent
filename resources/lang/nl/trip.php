<?php

return [
    // Page titles
    'title_index' => 'Reizen',
    'title_create' => 'Nieuwe reis',
    'title_show' => 'Reis details',
    'title_edit' => 'Reis bewerken',

    // Flash messages
    'created' => 'Reis is aangemaakt',
    'updated' => 'Reis is aangepast',
    'deleted' => 'Reis is verwijderd',

    // Traveler
    'traveler' => [
        'adult' => 'Volwassene',
        'child' => 'Kind',
    ],

    // Items
    'item' => [
        'type' => [
            'inclusion' => 'Inclusief',
            'exclusion' => 'Exclusief',
            'optional' => 'Optioneel',
        ],
        'inclusion' => [
            'itinerary' => 'Persoonlijk samengesteld reisplan',
            'background_info' => 'Een informatiepakket met routebeschrijving, achtergrond en tips ter plaatse',
            'train_reservations' => 'Zitplaatsreservering waar dat kan',
        ],
        'exclusion' => [
            'fees' => [
                'booking' => 'Boekingskosten - :amount per boeking',
                'guarantee_fund' => 'STO garantiefonds - :amount per boeking',
                'emergency_fund' => 'Calamiteitenfonds - :amount per boeking',
            ],
            'additional_meals' => 'Overige dranken & maaltijden',
            'excursions' => 'Overige activiteiten & excursies',
            'transfers' => 'Lokale verplaatsingen',
            'personal_expenses' => 'Persoonlijke uitgaven',
            'travel_cancellation_insurance' => 'Reis- en/of annuleringsverzekering',
            'local_tourist_tax' => 'Eventuele lokale toeristenbelasting',
        ],
    ],

    // Forms
    'forms' => [
        'tabs' => [
            'items' => 'Inclusies & Exclusies',
        ],
        'sections' => [
            'items' => [
                'inclusions_title' => 'Inbegrepen',
                'exclusions_title' => 'Niet inbegrepen',
            ],
        ],
        'fields' => [
            'trip_items' => [
                'placeholder' => 'Voer item beschrijving in',
            ],
        ],
    ],
    'practical-info' => [
        'sections' => [
            'travel_period' => 'Reisperiode',
            'departure_dates' => 'Vertrekdata',
            'outbound_return' => 'Heen- en terugreis',
            'transport' => 'Vervoer tijdens de reis',
            'accommodation' => 'Logies',
        ],
    ],

    // Transport
    'transport' => [
        'train' => 'Trein',
        'ferry' => 'Veerboot',
        'bus' => 'Bus',
        'taxi' => 'Taxi',
        'transfer' => 'Transfer',
        'airplane' => 'Vliegtuig',
    ],
];
