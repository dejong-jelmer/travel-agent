<?php

return [
    'custom' => [
        // Newsletter
        'name.max' => 'De ingevulde naam is te lang.',
        'email.unique' => 'Er is al een nieuwsbrief inschrijving onder dit e-mailadres.',
        'email.required' => 'Vul een e-mailadres in.',
        'email.email' => 'Het lijkt erop dat dit geen geldig e-mailadres is.',

        // Booking
        // Datum & bevestiging
        'departure_date.required' => 'Selecteer een vertrekdatum.',
        'departure_date.date' => 'De geselecteerde datum is ongeldig.',
        'departure_date.after' => 'De vertrekdatum kan niet in het verleden liggen.',
        'has_confirmed.accepted' => 'Je moet nog akkoord gaan.',
        'has_accepted_conditions.accepted' => 'Je moet nog akkoord gaan met de algemene voorwaarde.',

        // Contactgegevens
        'contact.street.required' => 'Vul een straatnaam in.',
        'contact.house_number.required' => 'Vul een huisnummer in.',
        'contact.house_number.regex' => 'Vul een geldig huisnummer in.',
        'contact.postal_code.required' => 'Vul een postcode in.',
        'contact.postal_code.regex' => 'Voer een geldige postcode voor Nederlands of België in.',
        'contact.city.required' => 'Vul een plaatsnaam in.',
        'contact.email.required' => 'Vul een e-mailadres in.',
        'contact.email.email' => 'Voer een geldig e-mailadres in.',
        'contact.phone.required' => 'Vul een telefoonnummer in.',
        'contact.phone.phone' => 'Voer een geldig Nederlands of Belgisch telefoonnummer in.',

        // Reizigers volwassenen
        'travelers.*.*.first_name.required' => 'Voor deze reiziger moet een voornaam worden ingevuld.',
        'travelers.*.*.last_name.required' => 'Voor deze reiziger moet een achternaam worden ingevuld.',
        'travelers.*.*.birthdate.required' => 'Voor deze reiziger moet een geboortedatum worden ingevuld.',
        'travelers.*.*.birthdate.date_format' => 'Voor deze reiziger moet een geldige geboortedatum worden ingevuld.',
        'travelers.*.*.nationality.required' => 'Voor deze reiziger moet een nationaliteit worden ingevuld.',
        'travelers.adults.*.birthdate.before' => 'Volwassenen moeten minimaal 12 jaar of ouder zijn.',
        'travelers.adults.*.birthdate.after' => 'De geboortedatum moet niet langer dan 100 jaar geleden zijn.',
        'travelers.children.*.birthdate.after_or_equal' => 'Voor kinderen geldt een maximum leeftijd van 12 jaar, kinderen vanaf 12 jaar en ouder tellen mee als volwassenen.',
        'travelers.children.*.birthdate.before' => 'De geboortedatum kan niet in de toekomst liggen.',
        'travelers.children.*.birthdate' => 'Ongeldige geboortedatum.',
    ],
];
