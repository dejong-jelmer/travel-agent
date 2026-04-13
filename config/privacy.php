<?php

use Carbon\Carbon;

$date = Carbon::parse('03-04-2026');

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
