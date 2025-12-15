<?php

return [
    // Page titles (Admin)
    'title_index' => 'Boekingen',
    'title_show' => 'Boeking details',
    'title_edit' => 'Boeking bewerken',

    // Page titles (Frontend)
    'title_received' => 'Boeking ontvangen',

    // Flash messages
    'created' => 'Je boeking is geslaagd! Je ontvangt een bevestigingsmail met meer details over je aanstaande reis.',
    'updated' => 'Boeking :reference is succesvol aangepast.',
    'deleted' => 'Boeking :reference is verwijderd.',

    // Status
    'status' => [
        'new' => 'Nieuw',
        'pending' => 'In afwachting',
        'confirmed' => 'Bevestigd',
        'canceled' => 'Geannuleerd',
        'completed' => 'Voltooid',
    ],
    'payment_status' => [
        'pending' => 'In afwachting',
        'partial_paid' => 'Gedeeltelijk betaald',
        'paid' => 'Betaald',
        'refunded' => 'Terugbetaald',
        'partially_refunded' => 'Gedeeltelijk terugbetaald',
        'failed' => 'Mislukt',
    ],
];
