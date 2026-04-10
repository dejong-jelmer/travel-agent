<?php

return [
    'title_index' => 'Requests',
    'thanks_page_title' => 'Request received',

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
    ],

    'mail' => [
        'confirmation_subject' => 'Your request for :trip has been received',
        'confirmation_greeting' => 'Dear :name,',
        'confirmation_intro' => 'Thank you for your request for :trip. I have received your message and will personally get in touch within two working days — by phone if you provided a number, otherwise by email.',
        'confirmation_next_steps' => 'In that conversation, we will look together at which period suits you best, what your preferences are, and what can make this trip special for you. Feel free to take your time to write down any questions, wishes or concerns; the more I know, the better I can tailor the trip to you personally.',
        'confirmation_summary_header' => 'What you requested',
        'confirmation_summary_intro' => 'Just to be sure, here is a summary of the details you shared with me. If something is incorrect or you would like to add anything, simply reply to this email and I will update it before I call you back.',
        'confirmation_what_to_expect_header' => 'What to expect from our call',
        'confirmation_what_to_expect_1' => 'We will discuss your preferred period together and which dates are realistic with the current train connections.',
        'confirmation_what_to_expect_2' => 'We will look together at the price indication and what is and is not included in the trip.',
        'confirmation_what_to_expect_3' => 'You will have ample opportunity to ask questions — there is no obligation to book straight away.',
        'confirmation_question' => 'Do you have a question in the meantime, or would you like to add something to your request? Feel free to call me or simply reply to this email.',
        'confirmation_phone_label' => 'Phone: ',
        'confirmation_closing' => 'Talk soon,',

        'notification_subject' => 'New request: :trip — :name',
        'notification_header' => 'New trip request received',
        'notification_subheader' => 'Trip: :trip',
        'notification_received_at' => 'Received at',

        'notification_section_trip' => 'Trip',
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
        'notification_not_specified' => 'Not specified',
        'notification_no_notes' => 'No notes provided.',

        'notification_reply_subject' => 'Your request for :trip',
        'notification_reply_button' => 'Reply directly via email',
    ],
];
