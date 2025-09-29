<?php

namespace App\DTO\Traits;

trait ArrayableDTO
{
    public function toArray(): array
    {
        $vars = get_object_vars($this);

        return array_map(function ($value) {
            if (is_array($value)) {
                return array_map(fn ($v) => $v instanceof self ? $v->toArray() : $v, $value);
            }

            return $value instanceof self ? $value->toArray() : $value;
        }, $vars);
    }
}
