<?php

return [
    // Page titles (Admin)
    'title_index' => 'Bookings',
    'title_show' => 'Booking details',
    'title_edit' => 'Edit booking',

    // Page titles (Frontend)
    'title_received' => 'Booking received',

    // Flash messages
    'created' => 'Your booking was successful! You will receive a confirmation email with more details about your upcoming trip.',
    'updated' => 'Booking :reference has been successfully updated.',
    'deleted' => 'Booking :reference has been deleted.',

    // Status
    'status' => [
        "new" => "New",
        "pending" => "Pending",
        "confirmed" => "Confirmed",
        "canceled" => "Canceled",
        "completed" => "Completed",
    ],
    'payment_status' => [
        'pending' => 'Pending',
        'partial_paid' => 'Partially paid',
        'paid' => 'Paid',
        'refunded' => 'Refunded',
        'partially_refunded' => 'Partially refunded',
        'failed' => 'Failed',
    ]
];
