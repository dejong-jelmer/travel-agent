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
        if (!$request->is('admin*')) {
            App::setLocale('nl');
            return $next($request);
        }

        $availableLocales = availableLocales() ?: ['nl', 'en'];
        $currentLocale = Session::get('locale');

        $locale = in_array($currentLocale, $availableLocales)
            ? $currentLocale
            : $request->getPreferredLanguage($availableLocales);

        $locale = $locale ?? config('app.locale');

        App::setLocale($locale);

        if ($currentLocale !== $locale) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}
