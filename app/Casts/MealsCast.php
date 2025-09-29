<?php

namespace App\Casts;

use App\Enums\Meals;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MealsCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return collect(json_decode($value ?? '[]', true))
            ->map(fn ($meal) => Meals::from($meal))
            ->all();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return json_encode(collect($value)
            ->map(fn ($meal) => $meal instanceof Meals ? $meal->value : $meal)
            ->all());
    }
}
