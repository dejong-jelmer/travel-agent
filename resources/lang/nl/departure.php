<?php

return [
    // Why a booking cannot get places on a departure
    'booking' => [
        'required' => 'Kies een vertrek voor deze groepsreis.',
        'cancelled' => 'Dit vertrek is geannuleerd.',
        'departed' => 'Dit vertrek is al vertrokken.',
        'full' => 'Dit vertrek is vol.',
        'no_price' => 'Dit vertrek heeft geen prijs: stel een prijs per persoon in op het vertrek, of een prijs op de reis voor de vertrekdatum.',
        'not_enough_seats' => 'Op dit vertrek is nog plek voor :available, deze boeking heeft :requested plekken nodig.',
    ],

    // Mail to the admin
    'mail' => [
        'minimum_reached' => [
            'subject' => 'Minimum gehaald: :trip op :date',
            'label' => 'Minimum aantal deelnemers gehaald',
            'intro' => 'Dit vertrek heeft het minimum aantal bevestigde deelnemers gehaald.',
            'action' => 'Het vertrek is nog niet gegarandeerd. Dat doe je zelf, zodra je zeker weet dat het doorgaat.',
        ],
        'section_departure' => 'Het vertrek',
        'label_trip' => 'Reis',
        'label_period' => 'Periode',
        'label_confirmed' => 'Bevestigd',
        'label_occupied' => 'Bezet',
        'label_decision_date' => 'Beslisdatum',
        'confirmed_of_minimum' => ':confirmed deelnemers, minimum :minimum',
        'occupied_of_maximum' => ':occupied van :maximum plekken',
        'button' => 'Bekijk de reis',
        'footer' => 'Automatische notificatie over een vertrek van een groepsreis.',
    ],
];
