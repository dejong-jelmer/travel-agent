<?php

namespace App\Casts;

use App\Enums\Transport;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class TransportCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return collect(json_decode($value ?? '[]', true))
            ->map(fn ($transport) => Transport::from($transport))->all();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value) || (is_array($value) && empty($value))) {
            return null;
        }

        return json_encode(collect($value)
            ->map(fn ($transport) => $transport instanceof Transport ? $transport->value : $transport)
            ->all());
    }
}
