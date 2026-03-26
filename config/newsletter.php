<?php

return [
    'subscription' => [
        'confirmation_expires_after' => 48,
<<<<<<< HEAD
        'retention_months' => 3,  // Months after unsubscribe before data is purged
=======
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
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
