<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'locale' => [
                'required',
                'string',
                Rule::in(config('app.available_locales', ['nl', 'en'])),
            ],
        ]);

        $locale = $request->input('locale');

        App::setLocale($locale);
        Session::put('locale', $locale);

        return back();
    }
}
