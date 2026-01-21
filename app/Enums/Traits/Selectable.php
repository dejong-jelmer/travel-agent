<?php

namespace App\Enums\Traits;

trait Selectable
{
    public static function options(): array
    {
        return collect(self::cases())
            ->map(function ($case) {
                $option = [
                    'id' => $case->value,
                    'name' => method_exists($case, 'label')
                        ? $case->label()
                        : $case->value,
                ];

                if (method_exists($case, 'extraOptions')) {
                    $option = array_merge($option, $case->extraOptions());
                }

                return $option;
            })
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
