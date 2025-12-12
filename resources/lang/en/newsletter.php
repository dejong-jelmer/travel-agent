<?php

return [
    'subscriber' => [
        // Page titles (Admin)
        'title_index' => 'Newsletter Subscribers',

        // Flash messages
        'deleted' => 'The newsletter subscriber has been deleted.',

        // Status
        'status' => [
            'active' => 'Active',
            'pending' => 'Pending',
            'expired' => 'Expired',
            'unsubscribed' => 'Unsubscribed',
        ],
    ],
    'subscription' => [
        // Page titles
        'title_confirmed' => 'Subscription confirmed',
        'title_unsubscribed' => 'Unsubscribed',

        // Flash messages
        'subscribed' => 'You have been subscribed to the newsletter',
        'confirmed' => 'Your newsletter subscription has been confirmed',
        'unsubscribed' => 'You have been unsubscribed from the newsletter',
        'confirmation_resend' => 'Confirmation email has been resent',
    ],
    'campaign' => [
        // Page titles
        'title_index' => 'Newsletter campaigns overview',
        'title_create' => 'Create new newsletter campaign',
        'title_edit' => 'Edit existing newsletter campaign',

        // Flash messages
        'created' => 'Campaign has been created',
        'updated' => 'Campaign has been updated',
        'deleted' => 'Campaign has been deleted',
        'sent' => 'This newsletter campaign has been sent to all subscribers',
        'sent_failed' => 'An error occurred while sending this newsletter campaign: ":error_message"',
        'test_email_sent' => 'Test email has been sent to :email',
        'test_email_failed' => 'An error occurred while sending test email',
        'no_email' => 'There is no user email address to send the email to',

        // Exceptions
        'campaign_already_sent' => 'Campaign with id :campaign_id has already been sent or queued.',

        // Status
        'status' => [
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'queued' => 'Queued',
            'sent' => 'Sent',
            'failed' => 'Failed',
        ],
    ],
];
