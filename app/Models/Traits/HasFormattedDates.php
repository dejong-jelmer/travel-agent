<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasFormattedDates
{
    protected function getFormattedDate(string $attribute): ?string
    {
        $config = $this->formattedDates[$attribute] ?? [];
        $format = $config['format'] ?? 'LLLL';
        $locale = $config['locale'] ?? config('app.locale');

        return $this->{$attribute}
            ? ucfirst($this->{$attribute}->locale($locale)->isoFormat($format))
            : null;
    }
}
