<?php

use Carbon\Carbon;

$date = Carbon::parse('24-04-2026');

return [
    'version' => '1.2',
    'updated' => $date->locale('nl')->isoFormat('MMMM YYYY'),
];
