<?php

return [
    // Page titles (Admin)
    'title_index' => 'Vertrekken',
    'title_show' => 'Deelnemerslijst',

    // Flash messages
    'created' => 'Het vertrek is aangemaakt.',
    'updated' => 'Het vertrek is aangepast.',
    'deleted' => 'Het vertrek is verwijderd.',
    'guaranteed' => 'Het vertrek is gegarandeerd.',
    'cancelled' => '{0} Het vertrek is geannuleerd. Er waren geen boekingen om te annuleren.|{1} Het vertrek is geannuleerd, samen met :count boeking.|[2,*] Het vertrek is geannuleerd, samen met :count boekingen.',

    'validation' => [
        'not_a_group_trip' => 'Alleen een groepsreis heeft vertrekken. Zet de reisvorm van deze reis eerst op groepsreis.',
        'decision_date_too_late' => 'De beslisdatum mag voor dit vertrek niet later zijn dan :date.',
        'deadline_passed' => 'De uiterste beslisdatum voor dit vertrek (:date) is al voorbij, dus het minimum aantal deelnemers kun je niet meer inroepen. Maak het vertrek direct gegarandeerd aan.',
        'not_editable' => 'Een geannuleerd of afgerond vertrek kan niet meer worden aangepast.',
        'max_below_occupied' => 'Er zijn al :occupied plekken bezet. Het maximum kan daar niet onder komen.',
        'not_cancellable' => 'Dit vertrek is al geannuleerd of al vertrokken.',
        'not_guaranteeable' => 'Een geannuleerd of afgerond vertrek kan niet worden gegarandeerd.',
        'has_bookings' => 'Dit vertrek heeft boekingen en kan niet worden verwijderd. Annuleer het vertrek als het niet doorgaat.',
    ],

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
        'digest' => [
            'subject' => '{1} :count vertrek vraagt je aandacht|[2,*] :count vertrekken vragen je aandacht',
            'label' => 'Dagelijks overzicht',
            'title' => 'Vertrekken die een besluit vragen',
            'intro' => 'Voor deze vertrekken moet je iets beslissen of regelen.',
            'groups' => [
                'overdue' => [
                    'title' => 'Beslisdatum voorbij',
                    'help' => 'De beslisdatum is verstreken zonder besluit. Garandeer het vertrek of annuleer het; annuleren vanwege te weinig deelnemers kan alleen nog zolang de wettelijke termijn niet voorbij is.',
                ],
                'due_soon' => [
                    'title' => 'Beslisdatum nadert, minimum niet gehaald',
                    'help' => 'Binnenkort moet je beslissen en het minimum aantal bevestigde deelnemers is nog niet gehaald.',
                ],
                'awaits_guarantee' => [
                    'title' => 'Minimum gehaald, wacht op jouw garantie',
                    'help' => 'Deze vertrekken hebben genoeg bevestigde deelnemers. Garandeer ze zodra je zeker weet dat ze doorgaan.',
                ],
            ],
            'row' => ':confirmed bevestigd, minimum :minimum · :occupied van :maximum plekken bezet · beslisdatum :date',
            'button' => 'Bekijk alle vertrekken',
            'footer' => 'Dagelijks overzicht van vertrekken van groepsreizen. Je krijgt deze mail alleen als er iets te doen is.',
        ],
        'section_departure' => 'Het vertrek',
        'label_trip' => 'Reis',
        'label_period' => 'Periode',
        'label_confirmed' => 'Bevestigd',
        'label_occupied' => 'Bezet',
        'label_decision_date' => 'Beslisdatum',
        'confirmed_of_minimum' => ':confirmed deelnemers, minimum :minimum',
        'occupied_of_maximum' => ':occupied van :maximum plekken',
        'button' => 'Bekijk het vertrek',
        'footer' => 'Automatische notificatie over een vertrek van een groepsreis.',
    ],
];
