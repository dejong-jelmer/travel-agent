<?php

namespace App\Casts;

use App\Enums\Meal;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MealCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return collect(json_decode($value ?? '[]', true))
            ->map(fn ($meal) => Meal::from($meal))->all();
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
            ->map(fn ($meal) => $meal instanceof Meal ? $meal->value : $meal)
            ->all());
    }
}
