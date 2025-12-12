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
        $locale = $request->input('locale')
            ?? Session::get('locale')
            ?? $request->getPreferredLanguage(
                config('app.available_locales', ['nl', 'en'])
            )
            ?? config('app.locale');

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
}
