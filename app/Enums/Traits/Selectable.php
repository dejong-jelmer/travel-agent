<?php

namespace App\Enums\Traits;

trait Selectable
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(function (self $case): array {
                $option = [
                    'id' => $case->value,
                    'name' => method_exists($case, 'label') // @phpstan-ignore function.alreadyNarrowedType
                        ? $case->label()
                        : $case->value,
                ];

                if (method_exists($case, 'extraOptions')) { // @phpstan-ignore function.alreadyNarrowedType, function.impossibleType
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
