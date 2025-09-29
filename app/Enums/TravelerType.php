<?php

namespace App\Enums;

enum TravelerType: string
{
    case Adult = 'adult';
    case Child = 'child';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromKey(string $key): self
    {
        return match (strtolower($key)) {
            'adult'   => self::Adult,
            'child' => self::Child,
            default => throw new \InvalidArgumentException("Unknown traveler type: {$key}"),
        };
    }
}
