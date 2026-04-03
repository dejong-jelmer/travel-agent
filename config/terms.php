<?php

use Carbon\Carbon;

$date = Carbon::parse('03-04-2026');

return [
    'version' => '1.1',
    'updated' => $date->locale('nl')->isoFormat('MMMM YYYY'),
];
