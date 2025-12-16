<?php

if (! function_exists('randomPrice')) {
    function randomPrice(int $min = 200, int $max = 5000): float
    {
        return (float) fake()->randomFloat(2, $min, $max);
    }
}
