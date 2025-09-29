<?php

namespace App\Http\Requests;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip.id' => ['required', Rule::exists(Product::class, 'id')],
            // Selectie datum & bevestiging
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'confirmed' => ['accepted'],

            // Contactgegevens
            'contact.street' => ['required', 'string', 'min:3', 'max:255'],
            'contact.house_number' => ['required', 'regex:/^\d+[a-zA-Z0-9\-]*$/'],
            'contact.addition' => ['nullable', 'string', 'max:10', 'regex:/^[0-9A-Za-z\s\-]+$/i'],
            'contact.postal' => ['required', 'regex:/^(?:\d{4}\s?[A-Z]{2}|[1-9]\d{3})$/i'],
            'contact.city' => ['required', 'string', 'min:3', 'max:255'],
            'contact.email' => ['required', 'email:rfc,dns'],
            'contact.phone' => ['required', 'string', 'phone:NL,BE'],

            // Reizigers volwassenen
            'travelers.adults.*.id' => ['integer', 'nullable'],
            'travelers.adults.*.first_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.adults.*.last_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.adults.*.birthdate' => [
                'required',
                'date',
                'before_or_equal:'.now()->subYears(12)->format('Y-m-d'),
            ],
            'travelers.adults.*.nationality' => ['required', 'string', 'min:2', 'max:255'],
            'travelers.adults.*.full_name' => ['required', 'string', 'min:3', 'max:255'],

            // Main booker
            'main_booker' => [
                'required',
                'integer',
                // should always be a valid index for travelers.adults[]
                function ($attribute, $value, $fail) {
                    $adults = $this->input('travelers.adults', []);
                    if (! array_key_exists($value, $adults)) {
                        $fail("De gekozen {$attribute} is ongeldig.");
                    }
                    // should always be 18 years or up
                    if ($adults[$value]) {
                        if (array_key_exists('birthdate', $adults[$value])) {
                            $birthdate = Carbon::parse($adults[$value]['birthdate']);
                            if ($birthdate->age < 18) {
                                $fail('De hoofdboeker moet minimaal 18 jaar oud zijn.');
                            }
                        }
                    }
                },
            ],

            // Reizigers kinderen
            'travelers.children.*.id' => ['integer', 'nullable'],
            'travelers.children.*.first_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.children.*.last_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.children.*.birthdate' => [
                'required',
                'date',
                'after_or_equal:'.now()->subYears(12)->format('Y-m-d'),
                'before_or_equal:'.now()->format('Y-m-d'),
            ],
            'travelers.children.*.nationality' => ['required', 'string', 'min:2', 'max:255'],
            'travelers.children.*.full_name' => ['required', 'string', 'min:3', 'max:255'],

        ];
    }

    public function messages(): array
    {
        return [
            // Datum & bevestiging
            'departure_date.required' => 'Selecteer een vertrekdatum.',
            'departure_date.date' => 'De geselecteerde datum is ongeldig.',
            'departure_date.after_or_equal' => 'De vertrekdatum mag niet in het verleden liggen.',
            'confirm.accepted' => 'Je moet de voorwaarden accepteren voordat je kunt boeken.',

            // Contactgegevens
            'contact.street.required' => 'Vul een straatnaam in.',
            'contact.postal.required' => 'Vul een postcode in.',
            'contact.postal.regex' => 'Voer een geldige Nederlandse of Belgische postcode in.',
            'contact.city.required' => 'Vul een plaatsnaam in.',
            'contact.email.required' => 'Vul een e-mailadres in.',
            'contact.email.email' => 'Voer een geldig e-mailadres in.',
            'contact.telephone.required' => 'Vul een telefoonnummer in.',
            'contact.telephone.phone' => 'Voer een geldig Nederlands of Belgisch telefoonnummer in.',

            // Reizigers volwassenen
            'travelers.adults.*.first_name.required' => 'Voor elke volwassene moet een voornaam worden ingevuld.',
            'travelers.adults.*.last_name.required' => 'Voor elke volwassene moet een achternaam worden ingevuld.',
            'travelers.adults.*.birthdate.required' => 'Voor elke volwassene moet een geboortedatum worden ingevuld.',
            'travelers.adults.*.birthdate.date' => 'De geboortedatum van een volwassene is ongeldig.',
            'travelers.adults.*.birthdate.before_or_equal' => 'Volwassenen moeten minimaal 12 jaar of ouder zijn.',
            'travelers.adults.*.nationality.required' => 'Voor elke volwassene moet een nationaliteit worden ingevuld.',

            // Reizigers kinderen
            'travelers.children.*.first_name.required' => 'Voor elk kind moet een voornaam worden ingevuld.',
            'travelers.children.*.last_name.required' => 'Voor elk kind moet een achternaam worden ingevuld.',
            'travelers.children.*.birthdate.required' => 'Voor elk kind moet een geboortedatum worden ingevuld.',
            'travelers.children.*.birthdate.date' => 'De geboortedatum van een kind is ongeldig.',
            'travelers.children.*.birthdate.after_or_equal' => 'Voor kinderen geldt een maximum leeftijd van 12 jaar, kinderen vanaf 12 jaar en ouder tellen mee als volwassenen.',
            'travelers.children.*.birthdate.before_or_equal' => 'De geboortedatum van een kind kan niet in de toekomst liggen.',
            'travelers.children.*.nationality.required' => 'Voor elk kind moet een nationaliteit worden ingevuld.',
        ];
    }
}
