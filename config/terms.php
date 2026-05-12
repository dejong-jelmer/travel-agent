<?php

use Carbon\Carbon;

$date = Carbon::parse('12-05-2026');

return [
    'version' => '1.3',
    'updated' => $date->locale('nl')->isoFormat('MMMM YYYY'),
];
