<?php

if (! function_exists('randomPrice')) {
    function randomPrice(int $min = 995, int $max = 11995): float
    {
        return (float) fake()->randomFloat(2, $min, $max);
    }
}
