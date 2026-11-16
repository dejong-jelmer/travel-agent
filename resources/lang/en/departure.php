<?php

return [
    // Why a booking cannot get places on a departure
    'booking' => [
        'required' => 'Choose a departure for this group trip.',
        'cancelled' => 'This departure is cancelled.',
        'departed' => 'This departure has already left.',
        'full' => 'This departure is full.',
        'not_enough_seats' => 'This departure has room for :available more, this booking needs :requested places.',
    ],

    // Mail to the admin
    'mail' => [
        'minimum_reached' => [
            'subject' => 'Minimum reached: :trip on :date',
            'label' => 'Minimum number of participants reached',
            'intro' => 'This departure has reached its minimum number of confirmed participants.',
            'action' => 'The departure is not guaranteed yet. You do that yourself, once you are sure it goes ahead.',
        ],
        'section_departure' => 'The departure',
        'label_trip' => 'Trip',
        'label_period' => 'Period',
        'label_confirmed' => 'Confirmed',
        'label_occupied' => 'Occupied',
        'label_decision_date' => 'Decision date',
        'confirmed_of_minimum' => ':confirmed participants, minimum :minimum',
        'occupied_of_maximum' => ':occupied of :maximum places',
        'button' => 'View the trip',
        'footer' => 'Automatic notification about a departure of a group trip.',
    ],
];
