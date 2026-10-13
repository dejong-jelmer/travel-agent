<?php

namespace App\Travel\Http\Requests;

use App\Travel\Data\InfoLink;
use App\Travel\Enums\TransitMode;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
            'info_link' => ['nullable', 'string', 'max:255', $this->validateInfoLink(...)],
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

    /**
     * An information link is a url, in which a variable such as {%leg_number%} may stand for a value of the leg: one of
     * InfoLink::VARIABLES. With their example values filled in, it has to pass as a url as Laravel's url rule does.
     */
    private function validateInfoLink(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $variable = fn (string $name) => '{%'.$name.'%}';
        if ($unknown = InfoLink::unknownVariables($value)) {
            $fail(sprintf(
                'De :attribute kent %s niet. Gebruik %s.',
                implode(', ', array_map($variable, $unknown)),
                implode(' of ', array_map($variable, array_keys(InfoLink::VARIABLES))),
            ));

            return;
        }

        if (! Str::isUrl((string) InfoLink::fill($value, InfoLink::VARIABLES))) {
            $fail('De :attribute is geen geldige url.');
        }
    }
}
