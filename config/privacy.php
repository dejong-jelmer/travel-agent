<?php

use Carbon\Carbon;

$date = Carbon::parse('2026-04-03');

return [
    'version' => '1.1',
    'updated' => $date->locale('nl')->isoFormat('MMMM YYYY'),
    'newsletter' => [
        'subscription' => [
            'retention_months' => 3,
        ],
    ],
    'booking' => [
        'retention_years' => 7,
        'special_requests_retention_days' => 7,
    ],
    'trip_request' => [
        'retention_years' => 1,
    ],
];
