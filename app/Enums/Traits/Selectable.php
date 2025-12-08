<?php

namespace App\Enums\Traits;

trait Selectable
{
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn ($case) => [
                'id' => $case->value,
                'name' => $case->value,
                'disabled' => method_exists($case, 'disabledOption')
                    ? $case->disabledOption()
                    : false,
            ])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    abstract public function disabledOption(): bool;
}
