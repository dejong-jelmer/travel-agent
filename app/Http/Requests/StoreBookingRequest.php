<?php

namespace App\Http\Requests;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
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
            'departure_date' => ['required', 'date', 'after:today'],
            'confirmed' => ['accepted'],
            'conditions' => ['accepted'],

            // Contactgegevens
            'contact.street' => ['required', 'string', 'min:3', 'max:255'],
            'contact.house_number' => ['required', 'regex:/^\d+[a-zA-Z0-9\-]*$/'],
            'contact.addition' => ['nullable', 'string', 'max:10', 'regex:/^[0-9A-Za-z\s\-]+$/i'],
            'contact.postal_code' => ['required', 'regex:/^(?:\d{4}\s?[A-Z]{2}|[1-9]\d{3})$/i'],
            'contact.city' => ['required', 'string', 'min:3', 'max:255'],
            'contact.email' => ['required', 'email:rfc,dns'],
            'contact.phone' => ['required', 'string', 'phone:NL,BE'],

            // Reizigers volwassenen
            'travelers.*.*.first_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.*.*.last_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.*.*.nationality' => ['required', 'string', 'min:2', 'max:255'],
            'travelers.*.*.full_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.adults.*.birthdate' => [
                'required',
                'date_format:d-m-Y',
                'before:'.now()->subYears(12)->format('d-m-Y'),
                'after:'.now()->subYears(100)->format('d-m-Y'),
            ],
            'travelers.children.*.birthdate' => [
                'required',
                'date_format:d-m-Y',
                'after_or_equal:'.now()->subYears(12)->format('d-m-Y'),
                'before:'.now()->format('d-m-Y'),
            ],
            // Main booker
            'main_booker' => ['required', 'integer'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Extra validation on the birthdate's
            $adults = $this->input('travelers.adults', []);
            $children = $this->input('travelers.children', []);
            foreach ($adults as $index => $adult) {
                try {
                    $birthdate = Carbon::createFromFormat('d-m-Y', $adult['birthdate']);
                } catch (\Exception $e) {
                    $validator->errors()->add("travelers.adults.$index.birthdate", 'Ongeldige geboortedatum.');

                    return;
                }
                if ($birthdate->gt(now()->subYears(12))) {
                    $validator->errors()->add("travelers.adults.$index.birthdate", 'Volwassenen moeten minimaal 12 jaar of ouder zijn.');
                }
                if ($birthdate->lt(now()->subYears(100))) {
                    $validator->errors()->add("travelers.adults.$index.birthdate", 'De geboortedatum moet niet langer dan 100 jaar geleden zijn.');
                }
            }
            foreach ($children as $index => $child) {
                try {
                    $birthdate = Carbon::createFromFormat('d-m-Y', $child['birthdate']);
                } catch (\Exception $e) {
                    $validator->errors()->add("travelers.children.$index.birthdate", 'Ongeldige geboortedatum.');

                    return;
                }
                if ($birthdate->lte(now()->subYears(12))) {
                    $validator->errors()->add("travelers.children.$index.birthdate", 'Voor kinderen geldt een maximum leeftijd van 12 jaar, kinderen vanaf 12 jaar en ouder tellen mee als volwassenen.');
                }
                if ($birthdate->gt(now())) {
                    $validator->errors()->add("travelers.children.$index.birthdate", 'De geboortedatum kan niet in de toekomst liggen.');
                }
            }
            $mainBookerIndex = $this->input('main_booker');

            if (isset($adults[$mainBookerIndex]['birthdate'])) {
                try {
                    $birthdate = Carbon::parse($adults[$mainBookerIndex]['birthdate']);
                } catch (\Exception $e) {
                    $validator->errors()->add("travelers.adults.$mainBookerIndex.birthdate", 'Voor deze reiziger moet een geldige geboortedatum worden ingevuld.');

                    return;
                }
                if ($birthdate->age < 18) {
                    $validator->errors()->add(
                        "travelers.adults.{$mainBookerIndex}.birthdate",
                        'De hoofdboeker moet minimaal 18 jaar oud zijn.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            // Datum & bevestiging
            'departure_date.required' => 'Selecteer een vertrekdatum.',
            'departure_date.date' => 'De geselecteerde datum is ongeldig.',
            'departure_date.after' => 'De vertrekdatum kan niet in het verleden liggen.',
            'confirmed.accepted' => 'Je moet nog akkoord gaan.',
            'conditions.accepted' => 'Je moet nog akkoord gaan met de algemene voorwaarde.',

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
        ];
    }
}
