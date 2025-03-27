<?php

if (! function_exists('price')) {
    function price(mixed $price)
    {
        return (string) number_format((float) $price, 2, '.', '');
    }
}
