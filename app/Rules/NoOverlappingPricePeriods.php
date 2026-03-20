<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoOverlappingPricePeriods implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            return;
        }

        $periods = collect($value)
            ->filter(fn ($p) => isset($p['valid_from'], $p['valid_until']))
            ->values();

        foreach ($periods as $i => $a) {
            foreach ($periods->slice($i + 1) as $b) {
                if ($a['valid_from'] <= $b['valid_until'] && $a['valid_until'] >= $b['valid_from']) {
                    $fail(__('validation.custom.prices.overlap'));

                    return;
                }
            }
        }
    }
}
