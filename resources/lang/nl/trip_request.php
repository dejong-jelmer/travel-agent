<?php

return [
    // Page titles
    'title_index' => 'Aanvragen',
    'title_edit' => 'Aanvragen bewerken',
    'thanks_page_title' => 'Aanvraag ontvangen',

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
    ],

    // Mail — Confirmation to requester
    'mail' => [
        'confirmation_subject' => 'Je aanvraag voor :trip is binnen',
        'confirmation_greeting' => 'Beste :name,',
        'confirmation_intro' => 'Bedankt voor je aanvraag voor :trip. Ik heb je bericht goed ontvangen en neem binnen twee werkdagen persoonlijk contact met je op — via de telefoon als je een nummer hebt ingevuld, anders per e-mail.',
        'confirmation_next_steps' => 'In dat gesprek kijken we samen naar welke periode het beste past, wat je voorkeuren zijn, en wat deze reis voor jou bijzonder kan maken. Neem gerust de tijd om al je vragen, wensen en eventuele bedenkingen op te schrijven; hoe meer ik weet, hoe beter ik de reis op jouw maat kan inrichten.',
        'confirmation_summary_header' => 'Wat je hebt aangevraagd',
        'confirmation_summary_intro' => 'Voor de zekerheid hieronder een overzicht van de gegevens die je aan me hebt doorgegeven. Klopt er iets niet, of wil je nog iets aanvullen? Stuur me gewoon een berichtje terug, dan pas ik het aan voordat ik je terugbel.',
        'confirmation_what_to_expect_header' => 'Wat kun je van het gesprek verwachten?',
        'confirmation_what_to_expect_1' => 'We bespreken samen je voorkeursperiode en welke datums realistisch zijn met de huidige treinverbindingen.',
        'confirmation_what_to_expect_2' => 'We kijken samen naar de prijsindicatie en wat er wel en niet bij de reis is inbegrepen.',
        'confirmation_what_to_expect_3' => 'Je krijgt ruim de gelegenheid om vragen te stellen — er is geen verplichting om direct te boeken.',
        'confirmation_question' => 'Heb je in de tussentijd nog een vraag of wil je iets toevoegen aan je aanvraag? Je mag me altijd bellen of gewoon op deze e-mail antwoorden.',
        'confirmation_phone_label' => 'Telefoon: ',
        'confirmation_closing' => 'Tot snel,',

        // Mail — Notification to owner
        'notification_subject' => 'Nieuwe aanvraag: :trip — :name',
        'notification_header' => 'Nieuwe reisaanvraag binnengekomen',
        'notification_subheader' => 'Reis: :trip',
        'notification_received_at' => 'Ontvangen op',

        'notification_section_trip' => 'Reis',
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
        'notification_not_specified' => 'Niet opgegeven',
        'notification_no_notes' => 'Geen bijzonderheden opgegeven.',

        'notification_reply_subject' => 'Je aanvraag voor :trip',
        'notification_reply_button' => 'Beantwoord direct via e-mail',
    ],
];
