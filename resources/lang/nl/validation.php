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
        'departure_date.blocked' => 'De geselecteerde vertrekdatum is niet beschikbaar voor deze reis.',
        'date_range_end' => 'De einddatum moet op of na de startdatum liggen.',
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
        'travelers.*.*.special_requests.max' => 'Bijzonderheden mogen maximaal 1000 tekens bevatten.',
        'travelers.*.*.special_requests_consent.required_with' => 'Je moet toestemming geven voor het verwerken van de door jou ingevulde bijzonderheden.',
        'travelers.*.*.special_requests_consent.accepted' => 'Je moet toestemming geven voor het verwerken van de door jou ingevulde bijzonderheden.',
        'special_requests_consent_required' => 'Je moet toestemming geven voor het verwerken van de door jou ingevulde bijzonderheden.',

        // Trip
        'highlights.*.title.required_with' => 'Vul een titel in bij dit hoogtepunt.',
        'highlights.*.title.distinct' => 'Deze titel is al gebruikt bij een ander hoogtepunt.',
        'highlights.*.category.enum' => 'Kies een geldige categorie bij dit hoogtepunt.',
        'highlights.*.category.required_with' => 'Kies een categorie bij dit eigen label.',
        'highlights.*.label.max' => 'Een eigen label mag maximaal :max tekens bevatten.',
        'key_facts.max' => 'Je kunt maximaal :max punten invullen.',
        'key_facts.*.label.required' => 'Vul een label in bij dit punt.',
        'key_facts.*.label.max' => 'Een label mag maximaal :max tekens bevatten.',
        'key_facts.*.value.required' => 'Vul een waarde in bij dit punt.',
        'key_facts.*.value.max' => 'Een waarde mag maximaal :max tekens bevatten.',
        'key_facts.*.icon.required' => 'Kies een icoon bij dit punt.',
        'key_facts.*.icon.enum' => 'Kies een geldig icoon bij dit punt.',
        'journey_section.in' => 'Kies een sectie die in de beschrijving staat, of kies "Geen".',
        'section_images.*.integer' => 'Kies een foto uit de galerij van deze reis, of kies "Geen foto".',
        'section_images.*.in' => 'Kies een foto uit de galerij van deze reis, of kies "Geen foto".',

        // Main booker
        'main_booker' => [
            'too_young' => 'De hoofdboeker moet minimaal 18 jaar oud zijn.',
        ],
        'prices' => [
            'overlap' => 'De prijsperiodes mogen elkaar niet overlappen.',
        ],
        'itinerary' => [
            'days' => [
                'overlap' => 'Deze reisdag(en) overlappen met een bestaand itinerary item.',
            ],
        ],
        'departure_date' => [
            'blocked' => 'Het is niet mogelijk deze reis aan te vangen op de gekozen vertrekdatum.',
        ],
    ],
];
