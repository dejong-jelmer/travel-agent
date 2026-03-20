<?php

namespace App\Services\Traits;

trait MergesRules
{
    protected static function mergeRules(array $base, array $additions): array
    {
        foreach ($additions as $key => $rules) {
            if (isset($base[$key])) {
                $base[$key] = array_merge(
                    $base[$key],
                    is_array($rules) ? $rules : [$rules]
                );
            } else {
                $base[$key] = is_array($rules) ? $rules : [$rules];
            }
        }

        return $base;
    }
}
