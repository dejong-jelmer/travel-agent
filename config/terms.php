<?php

use Carbon\Carbon;

$date = Carbon::parse('2026-05-13');

return [
    'version' => '1.4',
    'updated' => $date->locale('nl')->isoFormat('MMMM YYYY'),
];
