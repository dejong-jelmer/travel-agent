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

        $currentLocale = Session::get('locale');
        $locale = $currentLocale ?? $request->getPreferredLanguage(
            config('app.available_locales', ['nl', 'en'])
        )
            ?? config('app.locale');

        App::setLocale($locale);

        if ($currentLocale !== $locale) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}
