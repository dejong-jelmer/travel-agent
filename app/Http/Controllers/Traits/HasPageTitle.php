<?php

namespace App\Http\Controllers\Traits;

trait HasPageTitle
{
    protected function pageTitle(string $key): string
    {
        return __($key).' - '.config('app.name');
    }
}
