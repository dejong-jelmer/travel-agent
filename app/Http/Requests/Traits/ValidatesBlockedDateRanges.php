<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Validator;

trait ValidatesBlockedDateRanges
{
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('blocked_dates.dates', []) as $index => $entry) {
                if (! is_array($entry)) {
                    continue;
                }

                $start = $entry['start'] ?? null;
                $end   = $entry['end'] ?? null;

                if (! $start || ! $end) {
                    continue;
                }

                if ($validator->errors()->hasAny([
                    "blocked_dates.dates.{$index}.start",
                    "blocked_dates.dates.{$index}.end",
                ])) {
                    continue;
                }

                if ($start > $end) {
                    $validator->errors()->add(
                        "blocked_dates.dates.{$index}.end",
                        __('validation.custom.date_range_end')
                    );
                }
            }
        });
    }
}
