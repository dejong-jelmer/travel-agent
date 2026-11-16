<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Decision deadlines
    |--------------------------------------------------------------------------
    |
    | A departure of a group trip may be canceled when its minimum number of
    | participants is not reached, but the travelers have to be told in time.
    | The Package Travel Directive (EU) 2015/2302, article 12(3)(a), sets the
    | latest moment by the length of the trip: 20 days before the start for
    | trips of more than 6 days, 7 days for trips of 2 to 6 days and 48 hours
    | for trips of less than 2 days.
    |
    | The first entry whose min_duration_days the trip reaches applies, so
    | keep the list ordered from the longest trips to the shortest. A trip's
    | duration counts both the day of departure and the day of return.
    |
    */

    'decision_deadlines' => [
        ['min_duration_days' => 7, 'days_before_departure' => 20],
        ['min_duration_days' => 2, 'days_before_departure' => 7],
        ['min_duration_days' => 0, 'days_before_departure' => 2],
    ],

    /*
    |--------------------------------------------------------------------------
    | Decision warning
    |--------------------------------------------------------------------------
    |
    | The number of days before its decision date from which a departure that
    | has not reached its minimum is pointed out to the admin.
    |
    */

    'decision_warning_days' => 7,

];
