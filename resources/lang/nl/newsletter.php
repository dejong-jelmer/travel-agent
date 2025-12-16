<?php

return [
    'subscriber' => [
        // Page titles (Admin)
        'title_index' => 'Nieuwsbrief Abonnees',

        // Flash messages
        'deleted' => 'De nieuwsbrief abonnee is verwijderd.',

        // Status
        'status' => [
            'active' => 'Actief',
            'pending' => 'In afwachting',
            'expired' => 'Verlopen',
            'unsubscribed' => 'Uitgeschreven',
        ],
    ],
    'subscription' => [
        // Page titles
        'title_confirmed' => 'Inschrijving bevestigd',
        'title_unsubscribed' => 'Uitschrijving bevestigd',

        // Flash messages
        'subscribed' => 'Je bent ingeschreven voor de nieuwsbrief',
        'confirmed' => 'Je nieuwsbrief inschrijving is bevestigd',
        'unsubscribed' => 'Je bent uitgeschreven voor de nieuwsbrief',
        'confirmation_resend' => 'Bevestigingsmail is opnieuw verstuurd',
    ],
    'campaign' => [
        // Page titles
        'title_index' => 'Overzicht van nieuwsbrief campagnes',
        'title_create' => 'Nieuwe nieuwsbrief campagne aanmaken',
        'title_edit' => 'Bestaande nieuwsbrief campagne bewerken',

        // Flash messages
        'created' => 'Campagne is aangemaakt',
        'updated' => 'Campagne is aangepast',
        'deleted' => 'Campagne is verwijderd',
        'sent' => 'Deze nieuwsbrief campagne is verzonden naar alle abbonees',
        'sent_failed' => 'Er is een fout opgetreden bij het versturen van deze nieuwsbrief campagne: ":error_message"',
        'test_email_sent' => 'Test email is verzonden naar :email',
        'test_email_failed' => 'Er is een fout opgetreden bij het versturen van test email',
        'no_email' => 'Er is geen gebruikers email adres waar we de email naar toe kunnen sturen',

        // Exceptions
        'campaign_already_sent' => 'Campagne met id :campaign_id is al eerder verstuurd of in de wachtrij gezet.',

        // Status
        'status' => [
            'draft' => 'Ontwerp',
            'scheduled' => 'Gepland',
            'queued' => 'In de wachtrij',
            'sent' => 'Verzonden',
            'failed' => 'Mislukt',
        ],
    ],
];
