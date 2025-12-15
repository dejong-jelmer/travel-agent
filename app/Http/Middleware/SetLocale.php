<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale')
            ?? $request->getPreferredLanguage(
                config('app.available_locales', ['nl', 'en'])
            )
            ?? config('app.locale');

        $currentLocale = App::setLocale($locale);

        if ($currentLocale !== Session::get('locale')) {
            Session::put('locale', $currentLocale);
        }

        return $next($request);
    }
}
