<?php

namespace App\Models\Traits;

trait HasFormattedDates
{
    protected function getFormattedDate(string $attribute): ?string
    {
        $config = $this->formattedDates[$attribute] ?? [];
        $format = $config['format'] ?? 'LLLL';
        $locale = $config['locale'] ?? app()->getLocale();

        return $this->{$attribute}
            ? ucfirst($this->{$attribute}->locale($locale)->isoFormat($format))
            : null;
    }
}
