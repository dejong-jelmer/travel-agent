<?php

return [
    'first_name' => env('CONTACT_FIRST_NAME', ''),
    'last_name' => env('CONTACT_LAST_NAME', ''),
    'full_name' => env('CONTACT_FIRST_NAME', '').' '.env('CONTACT_LAST_NAME', ''),
    'phone' => env('CONTACT_PHONE', ''),
    'mail' => env('CONTACT_MAIL', ''),
    'address' => env('CONTACT_ADDRESS', ''),
    'postal_code' => env('CONTACT_POSTAL', ''),
    'city' => env('CONTACT_CITY', ''),
    'maps' => env('CONTACT_MAPS', ''),
    'kvk' => env('CONTACT_KVK', ''),
    'btw' => env('CONTACT_BTW', ''),

];
