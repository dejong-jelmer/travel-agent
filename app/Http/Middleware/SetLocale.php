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
        $availableLocales = config('app.available_locales', ['nl', 'en']);
        $currentLocale = Session::get('locale');

        // Set preferred locale
        $locale = $currentLocale
            ?: $request->getPreferredLanguage($availableLocales)
            ?: config('app.locale');

        // Validate or fallback to default
        if (! in_array($locale, $availableLocales)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        // Set session locale if changed
        if ($currentLocale !== $locale) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}
