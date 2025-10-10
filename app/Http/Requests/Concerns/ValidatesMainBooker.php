<?php

namespace App\Http\Requests\Concerns;

use Carbon\Carbon;

trait ValidatesMainBooker
{
    protected function validateMainBooker($validator): void
    {
        $adults = $this->input('travelers.adults', []);
        $mainBookerIndex = $this->input('main_booker');

        if (! isset($adults[$mainBookerIndex]['birthdate'])) {
            return;
        }

        $birthdateString = $adults[$mainBookerIndex]['birthdate'];

        try {
            $birthdate = Carbon::createFromFormat('d-m-Y', $birthdateString);
        } catch (\Exception $e) {
            $validator->errors()->add(
                "travelers.adults.$mainBookerIndex.birthdate",
                __('validation.custom.travelers.*.*.birthdate.date_format')
            );

            return;
        }

        if ($birthdate->age < 18) {
            $validator->errors()->add(
                "travelers.adults.$mainBookerIndex.birthdate",
                __('custom_validation.main_booker.birthdate.before')
            );
        }
    }

    public function withValidator($validator)
    {
        $validator->after(fn ($v) => $this->validateMainBooker($v));
    }
}
