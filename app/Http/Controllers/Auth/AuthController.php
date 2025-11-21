<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AuthController extends Controller
{
    private string $appName;

    public function __construct()
    {
        $this->appName = config('app.name');
    }

    public function login(Request $request): InertiaResponse|HttpResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return Inertia::location(session('url.intended', route('admin.dashboard')));
        }

        return Inertia::render('Auth/Login', [
            'title' => __('auth.title_login').' - '.$this->appName,
            'error' => true,
            'message' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
