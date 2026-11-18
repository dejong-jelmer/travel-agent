<?php

return [
    // Page titles
    'title_index' => 'Aanvragen',
    'title_edit' => 'Aanvragen bewerken',

    // Flash messages
    'updated' => 'Aanvraag is succesvol aangepast.',
    'deleted' => 'Aanvraag is verwijderd.',

    // Status labels (used by HasTranslatableLabel trait)
    'status' => [
        'new' => 'Nieuw',
        'contacted' => 'Gecontacteerd',
        'converted' => 'Omgezet naar boeking',
        'archived' => 'Gearchiveerd',
    ],

    // Validation messages
    'validation' => [
        'name_required' => 'Vul je naam in.',
        'name_max' => 'Je naam mag maximaal :max tekens bevatten.',
        'email_required' => 'Vul je e-mailadres in.',
        'email_email' => 'Het lijkt erop dat dit geen geldig e-mailadres is.',
        'phone_max' => 'Het telefoonnummer mag maximaal :max tekens bevatten.',
        'preferred_period_note_max' => 'De toelichting op je voorkeursperiode mag maximaal :max tekens bevatten.',
        'notes_max' => 'Je opmerkingen mogen maximaal :max tekens bevatten.',
        'consent_privacy_accepted' => 'Je moet nog akkoord gaan met het privacybeleid.',
        'departure_required' => 'Kies een vertrekdatum.',
        'departure_closed' => 'Voor dit vertrek kun je helaas geen aanvraag meer doen. Kies een andere datum.',
        'departure_full' => 'Dit vertrek is inmiddels vol. Je kunt nog wel laten weten dat je graag hoort als er een plek vrijkomt.',
        'not_enough_seats' => '{1} Op dit vertrek is nog plek voor :count reiziger.|[2,*] Op dit vertrek is nog plek voor :count reizigers.',
        'travelers_count_required' => 'Laat weten met hoeveel mensen je komt.',
        'travelers_count_max' => 'Bij een groepsreis kun je voor maximaal 6 reizigers een aanvraag doen.',
    ],

    // Mail - Confirmation to requester
    'mail' => [
        'confirmation_subject' => 'Je aanvraag voor :trip is binnen',
        'confirmation_title' => 'Je aanvraag is binnen',
        'confirmation_greeting' => 'Beste :name,',
        'confirmation_intro' => 'Bedankt voor je aanvraag voor :trip. Ik heb je bericht goed ontvangen en neem zo snel mogelijk contact met je op.',
        'confirmation_summary_header' => 'Wat je hebt aangevraagd',
        'confirmation_summary_intro' => 'Voor de zekerheid hieronder een overzicht van de gegevens die je aan me hebt doorgegeven. Klopt er iets niet, of wil je nog iets aanvullen? Stuur me gewoon een berichtje terug, dan pas ik het aan voordat ik je terugbel.',
        'confirmation_what_to_expect_header' => 'Wat kun je van het gesprek verwachten?',
        'confirmation_what_to_expect_1' => 'We bespreken samen je voorkeursperiode en welke data realistisch zijn met de huidige treinverbindingen.',
        'confirmation_what_to_expect_2' => 'We kijken samen naar de prijsindicatie en wat er wel en niet bij de reis is inbegrepen.',
        'confirmation_what_to_expect_3' => 'Je krijgt ruim de gelegenheid om vragen te stellen - er is geen verplichting om direct te boeken.',
        'confirmation_question' => 'Heb je in de tussentijd nog een vraag of wil je iets toevoegen aan je aanvraag? Je mag me altijd bellen of gewoon op deze e-mail antwoorden.',
        'confirmation_closing' => 'Tot snel,',

        // Mail - Notification to owner
        'notification_subject' => 'Nieuwe aanvraag: :trip - :name',
        'notification_header' => 'Nieuwe reisaanvraag binnengekomen',
        'notification_subheader' => 'Reis: :trip',
        'notification_received_at' => 'Ontvangen op',

        'notification_section_contact' => 'Contactgegevens',
        'notification_section_preferences' => 'Reisvoorkeuren',
        'notification_section_notes' => 'Bijzonderheden',

        'notification_label_trip' => 'Reis',
        'notification_label_name' => 'Naam',
        'notification_label_email' => 'E-mail',
        'notification_label_phone' => 'Telefoon',
        'notification_label_period' => 'Voorkeursperiode',
        'notification_label_period_note' => 'Extra toelichting',
        'notification_label_travelers' => 'Aantal reizigers',
        'notification_label_station' => 'Vertrekstation',

        'notification_travelers_more_than_8' => 'Meer dan 8 (exact aantal navragen)',
        'notification_label_departure' => 'Vertrek',
        'notification_label_interest' => 'Let op',
        'notification_label_single_room' => 'Eenpersoonskamer',
        'notification_travelers_more_than_6' => 'Meer dan 6 (exact aantal navragen)',
        'interest_only' => 'Dit vertrek is vol: wil horen als er een plek vrijkomt',
        'yes' => 'Ja',
        'no' => 'Nee',
        'confirmation_group_note' => 'Je aanvraag reserveert nog geen plek. Je plek is zeker zodra ik je aanvraag bevestig.',
        'confirmation_interest_note' => 'Dit vertrek is op dit moment vol. Komt er een plek vrij, dan laat ik het je weten.',
        'confirmation_what_to_expect_group_1' => 'Ik kijk of er op het vertrek van je keuze nog plek is.',
        'confirmation_what_to_expect_group_2' => 'We nemen samen door wat er bij de reis is inbegrepen en wat de reissom wordt.',
        'confirmation_what_to_expect_group_3' => 'Je krijgt ruim de gelegenheid om vragen te stellen - er is geen verplichting om direct te boeken.',
        'notification_not_specified' => 'Niet opgegeven',
        'notification_no_notes' => 'Geen bijzonderheden opgegeven.',

        'notification_reply_subject' => 'Je aanvraag voor :trip',
        'notification_reply_button' => 'Beantwoord direct via e-mail',
    ],
];
