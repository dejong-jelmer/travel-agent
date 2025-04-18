<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:100'],
            'text' => ['required', 'string', 'min:5', 'max:2000'],
            'telephone' => ['nullable', 'string', 'phone:LENIENT'],
        ];
    }

    /**
     * Get the validation messages that are returned on validation fail.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => ' Vul je hier je naam in.',
            'email.required' => 'Vul een e-mailadres in.',
            'email.email' => 'Het lijkt erop dat dit geen geldig e-mailadres is.',
            'phone' => 'Het lijkt erop dat dit een ongeldig telefoonnummer is.',
            'text.required' => 'Laat het berichtveld niet leeg.',
            'text.min' => 'Je bericht is te kort, probeer een bericht met ten minste :min karakters.',
            'text.max' => 'Je bericht is te lang, probeer niet meer dan :max karakters te gebruiken.',

        ];
    }
}
