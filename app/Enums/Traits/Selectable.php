<?php

namespace App\Enums\Traits;

trait Selectable
{
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($option) => [$option->value => $option->value])
            ->toArray();
    }
}
