<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EnumKeys implements ValidationRule
{
    public function __construct(private string $enumClass) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('The :attribute must be an array.');

            return;
        }

        $validKeys = array_column($this->enumClass::cases(), 'value');
        $invalidKeys = array_diff(array_keys($value), $validKeys);

        if (! empty($invalidKeys)) {
            $fail('The :attribute contains invalid keys: '.implode(', ', $invalidKeys));
        }
    }
}
