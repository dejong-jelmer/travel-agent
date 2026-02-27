<?php

namespace App\Models\Traits;

trait CastsStringArray
{
    protected function castStringArray(mixed $value): string
    {
        if (is_null($value)) {
            return '[]';
        }

        return json_encode(
            collect(
                ! is_array($value)
                    ? array_map('trim', explode(',', $value))
                    : $value,
            )->filter(fn ($item) => ! is_null($item) && trim($item) !== '')->values()->all()
        );
    }
}
