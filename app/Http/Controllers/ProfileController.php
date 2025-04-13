<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user()->load('roles');

        activity()
            ->useLog('Perfil')
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Acedeu à página de edição do perfil.");

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'userData' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telemovel' => $user->telemovel,
                'estado' => $user->estado,
                'role' => $user->getRoleNames()->first() ?? '—',
            ],
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $originalEmail = $user->email;

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        activity()
            ->useLog('Perfil')
            ->causedBy($user)
            ->withProperties([
                'nome' => $user->name,
                'email_antigo' => $originalEmail,
                'email_novo' => $user->email,
                'telemovel' => $user->telemovel,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Atualizou os dados do perfil.");

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $userId = $user->id;
        $userEmail = $user->email;

        activity()
            ->useLog('Perfil')
            ->causedBy($user)
            ->withProperties([
                'id' => $userId,
                'email' => $userEmail,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Eliminou a sua conta.");

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
