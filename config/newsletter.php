<?php

return [
    'subscription' => [
        'confirmation_expires_after' => 48,
    ],
    'campaign' => [
        'chunk_size' => 50,
        'send' => [
            'tries' => 3,
            'backoff' => [60, 300, 900],  // 1min, 5min, 15min
            'timeout' => 300,  // 5 minutes
        ],
    ],
];
