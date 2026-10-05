<?php

use Carbon\Carbon;

$date = Carbon::parse('2026-10-05');

return [
    'version' => '1.5',
    'updated' => $date->locale('nl')->isoFormat('MMMM YYYY'),
];
