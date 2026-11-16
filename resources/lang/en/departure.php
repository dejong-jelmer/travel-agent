<?php

return [
    // Page titles (Admin)
    'title_index' => 'Departures',
    'title_show' => 'Participant list',

    // Flash messages
    'created' => 'The departure has been created.',
    'updated' => 'The departure has been updated.',
    'deleted' => 'The departure has been deleted.',
    'guaranteed' => 'The departure is guaranteed.',
    'cancelled' => '{0} The departure is cancelled. There were no bookings to cancel.|{1} The departure is cancelled, along with :count booking.|[2,*] The departure is cancelled, along with :count bookings.',

    'validation' => [
        'not_a_group_trip' => 'Only a group trip has departures. Set the format of this trip to group trip first.',
        'decision_date_too_late' => 'The decision date of this departure may not be later than :date.',
        'deadline_passed' => 'The last decision date for this departure (:date) has passed, so the minimum number of participants can no longer be relied on. Create the departure as guaranteed.',
        'not_editable' => 'A cancelled or completed departure can no longer be changed.',
        'max_below_occupied' => ':occupied places are already taken. The maximum cannot go below that.',
        'not_cancellable' => 'This departure is already cancelled or has already left.',
        'not_guaranteeable' => 'A cancelled or completed departure cannot be guaranteed.',
        'has_bookings' => 'This departure has bookings and cannot be deleted. Cancel the departure when it does not go ahead.',
    ],

    // Why a booking cannot get places on a departure
    'booking' => [
        'required' => 'Choose a departure for this group trip.',
        'cancelled' => 'This departure is cancelled.',
        'departed' => 'This departure has already left.',
        'full' => 'This departure is full.',
        'no_price' => 'This departure has no price: set a price per person on the departure, or a price on the trip for the day of departure.',
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
        'button' => 'View the departure',
        'footer' => 'Automatic notification about a departure of a group trip.',
    ],
];
