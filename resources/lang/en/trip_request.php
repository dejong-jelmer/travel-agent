<?php

return [
    'title_index' => 'Requests',
    'title_edit' => 'Edit request',

    // Flash messages
    'updated' => 'Request updated successfully.',
    'deleted' => 'Request is deleted.',

    'status' => [
        'new' => 'New',
        'contacted' => 'Contacted',
        'converted' => 'Converted to booking',
        'archived' => 'Archived',
    ],

    'validation' => [
        'name_required' => 'Please enter your name.',
        'name_max' => 'Your name may not exceed :max characters.',
        'email_required' => 'Please enter your email address.',
        'email_email' => 'This does not appear to be a valid email address.',
        'phone_max' => 'The phone number may not exceed :max characters.',
        'preferred_period_note_max' => 'The period note may not exceed :max characters.',
        'notes_max' => 'Your notes may not exceed :max characters.',
        'consent_privacy_accepted' => 'You must agree to the privacy policy.',
        'departure_required' => 'Choose a departure date.',
        'departure_closed' => 'This departure can no longer be requested. Please choose another date.',
        'departure_full' => 'This departure has just filled up. You can still let me know you would like to hear when a place comes free.',
        'not_enough_seats' => '{1} This departure has room for :count more traveler.|[2,*] This departure has room for :count more travelers.',
        'travelers_count_required' => 'Let me know how many people are coming.',
        'travelers_count_max' => 'A group trip can be requested for at most 6 travelers.',
    ],

    'mail' => [
        'confirmation_subject' => 'Your request for :trip has been received',
        'confirmation_title' => 'Your request has been received',
        'confirmation_greeting' => 'Dear :name,',
        'confirmation_intro' => 'Thank you for your request for :trip. I have received your message and will get in touch as soon as possible.',
        'confirmation_summary_header' => 'What you requested',
        'confirmation_summary_intro' => 'Just to be sure, here is a summary of the details you shared with me. If something is incorrect or you would like to add anything, simply reply to this email and I will update it before I call you back.',
        'confirmation_what_to_expect_header' => 'What to expect from our call',
        'confirmation_what_to_expect_1' => 'We will discuss your preferred period together and which dates are realistic with the current train connections.',
        'confirmation_what_to_expect_2' => 'We will look together at the price indication and what is and is not included in the trip.',
        'confirmation_what_to_expect_3' => 'You will have ample opportunity to ask questions - there is no obligation to book straight away.',
        'confirmation_question' => 'Do you have a question in the meantime, or would you like to add something to your request? Feel free to call me or simply reply to this email.',
        'confirmation_closing' => 'Talk soon,',

        'notification_subject' => 'New request: :trip - :name',
        'notification_header' => 'New trip request received',
        'notification_subheader' => 'Trip: :trip',
        'notification_received_at' => 'Received at',

        'notification_section_contact' => 'Contact details',
        'notification_section_preferences' => 'Trip preferences',
        'notification_section_notes' => 'Notes',

        'notification_label_trip' => 'Trip',
        'notification_label_name' => 'Name',
        'notification_label_email' => 'Email',
        'notification_label_phone' => 'Phone',
        'notification_label_period' => 'Preferred period',
        'notification_label_period_note' => 'Additional note',
        'notification_label_travelers' => 'Number of travelers',
        'notification_label_station' => 'Departure station',

        'notification_travelers_more_than_8' => 'More than 8 (ask for exact number)',
        'notification_label_departure' => 'Departure',
        'notification_label_interest' => 'Note',
        'notification_label_single_room' => 'Single room',
        'notification_travelers_more_than_6' => 'More than 6 (ask for exact number)',
        'interest_only' => 'This departure is full: would like to hear when a place comes free',
        'yes' => 'Yes',
        'no' => 'No',
        'confirmation_group_note' => 'Your request does not reserve a place yet. Your place is certain once I confirm your request.',
        'confirmation_interest_note' => 'This departure is full at the moment. When a place comes free, I will let you know.',
        'confirmation_what_to_expect_group_1' => 'I check whether there is still room on the departure you chose.',
        'confirmation_what_to_expect_group_2' => 'We go through what the trip includes and what the total price will be.',
        'confirmation_what_to_expect_group_3' => 'You get plenty of room to ask questions - there is no obligation to book right away.',
        'notification_not_specified' => 'Not specified',
        'notification_no_notes' => 'No notes provided.',

        'notification_reply_subject' => 'Your request for :trip',
        'notification_reply_button' => 'Reply directly via email',
    ],
];
