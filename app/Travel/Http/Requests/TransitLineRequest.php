<?php

namespace App\Travel\Http\Requests;

use App\Travel\Enums\TransitMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Stores or updates a transit line. UpdateTransitLine keeps the mode of a line that legs already use.
 */
class TransitLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mode' => ['required', Rule::enum(TransitMode::class)],
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'info_link' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'color.regex' => 'Geef de kleur als hexcode, zoals #e4003f.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'mode' => 'vervoermiddel',
            'name' => 'naam',
            'color' => 'kleur',
            'info_link' => 'informatielink',
        ];
    }
}
