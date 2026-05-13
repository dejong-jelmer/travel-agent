<?php

use Carbon\Carbon;

$date = Carbon::parse('13-05-2026');

return [
    'version' => '1.4',
    'updated' => $date->locale('nl')->isoFormat('MMMM YYYY'),
];
