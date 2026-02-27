<?php

return [
    'custom' => [
        // Newsletter
        'name.max' => 'The entered name is too long.',
        'email.unique' => 'There is already a newsletter subscription with this email address.',
        'email.required' => 'Please enter an email address.',
        'email.email' => 'This does not appear to be a valid email address.',

        // Booking
        // Date & confirmation
        'departure_date.required' => 'Please select a departure date.',
        'departure_date.date' => 'The selected date is invalid.',
        'departure_date.after' => 'The departure date cannot be in the past.',
        'departure_date.blocked' => 'The selected departure date is not available for this trip.',
        'date_range_end' => 'The end date must be on or after the start date.',
        'has_confirmed.accepted' => 'You must still agree.',
        'has_accepted_conditions.accepted' => 'You must still agree to the terms and conditions.',

        // Contact details
        'contact.street.required' => 'Please enter a street name.',
        'contact.house_number.required' => 'Please enter a house number.',
        'contact.house_number.regex' => 'Please enter a valid house number.',
        'contact.postal_code.required' => 'Please enter a postal code.',
        'contact.postal_code.regex' => 'Please enter a valid postal code for the Netherlands or Belgium.',
        'contact.city.required' => 'Please enter a city name.',
        'contact.email.required' => 'Please enter an email address.',
        'contact.email.email' => 'Please enter a valid email address.',
        'contact.phone.required' => 'Please enter a phone number.',
        'contact.phone.phone' => 'Please enter a valid Dutch or Belgian phone number.',

        // Travelers adults
        'travelers.*.*.first_name.required' => 'A first name must be entered for this traveler.',
        'travelers.*.*.last_name.required' => 'A last name must be entered for this traveler.',
        'travelers.*.*.birthdate.required' => 'A date of birth must be entered for this traveler.',
        'travelers.*.*.birthdate.date_format' => 'A valid date of birth must be entered for this traveler.',
        'travelers.*.*.nationality.required' => 'A nationality must be entered for this traveler.',
        'travelers.adults.*.birthdate.before' => 'Adults must be at least 12 years or older.',
        'travelers.adults.*.birthdate.after' => 'The date of birth must not be more than 100 years ago.',
        'travelers.children.*.birthdate.after_or_equal' => 'For children, a maximum age of 12 years applies, children aged 12 and over count as adults.',
        'travelers.children.*.birthdate.before' => 'The date of birth cannot be in the future.',
        'travelers.children.*.birthdate' => 'Invalid date of birth.',

        // Main booker
        'main_booker' => [
            'too_young' => 'The main booker must be at least 18 years old.',
        ],
        'itinerary' => [
            'days' => [
                'overlap' => 'These travel day(s) overlap with an existing itinerary item.',
            ],
        ],
    ],
];
