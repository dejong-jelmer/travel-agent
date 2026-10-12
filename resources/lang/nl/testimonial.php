<?php

return [
    // Page titles
    'title_index' => 'Ervaringen',
    'title_create' => 'Nieuwe ervaring',
    'title_edit' => 'Ervaring bewerken',

    // Flash messages
    'created' => 'Ervaring is aangemaakt',
    'updated' => 'Ervaring is aangepast',
    'deleted' => 'Ervaring is verwijderd',
    'published' => 'Ervaring is gepubliceerd',
    'unpublished' => 'Ervaring is offline gehaald',

    // Relationships
    'relationship' => [
        'customer' => 'Klant',
        'personal' => 'Persoonlijk (familie, vriend)',
    ],

    'validation' => [
        'quote_required' => 'Vul een citaat in.',
        'quote_max' => 'Het citaat mag maximaal :max tekens bevatten.',
        'body_max' => 'Het volledige verslag mag maximaal :max tekens bevatten.',
        'author_name_required' => 'Vul de naam van de schrijver in.',
        'relationship_required' => 'Kies de relatie van de schrijver.',
        'relationship_enum' => 'Kies een geldige relatie.',
        'author_context_required' => 'Vul de context in bij een persoonlijk verhaal, zodat duidelijk is dat het geen klantreview is.',
        'author_context_max' => 'De context mag maximaal :max tekens bevatten.',
        'link_url_url' => 'Vul een geldige link in die begint met http:// of https://.',
    ],
];
