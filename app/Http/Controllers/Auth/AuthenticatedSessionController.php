<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($request->throttleKey());

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => __('As credenciais estão incorretas.'),
            ]);
        }

        // Se o utilizador tiver 2FA ativo e confirmado, redireciona para o challenge
        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            session(['login.id' => $user->getKey()]);

            RateLimiter::clear($request->throttleKey());

            return redirect()->route('two-factor.login');
        }

        // Login normal
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        RateLimiter::clear($request->throttleKey());

        return redirect()->intended(config('fortify.home'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
