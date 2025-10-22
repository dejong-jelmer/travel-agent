<?php

namespace App\Models\Traits;

trait HasUniqueTokens
{
    protected function generateUniqueToken(string $column, int $bytes = 32): string
    {
        do {
            $token = bin2hex(random_bytes($bytes));
        } while (static::where($column, $token)->exists());

        return $token;
    }
}
