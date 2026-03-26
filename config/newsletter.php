<?php

return [
    'subscription' => [
        'confirmation_expires_after' => 48,
        'retention_months' => 3,  // Months after unsubscribe before data is purged
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
