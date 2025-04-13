<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UtilizadorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('termo')) {
            $query->where('name', 'like', '%' . $request->termo . '%')
                ->orWhere('email', 'like', '%' . $request->termo . '%')
                ->orWhere('telemovel', 'like', '%' . $request->termo . '%');
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        $query->orderBy($sort, $direction);

        $utilizadores = $query->paginate(10)->withQueryString();

        activity()
            ->useLog('Utilizadores')
            ->causedBy(auth()->user())
            ->withProperties([
                'termo' => $request->input('termo'),
                'estado' => $request->input('estado'),
                'sort' => $sort,
                'direction' => $direction,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à listagem de utilizadores.');

        return Inertia::render('Utilizadores/Index', [
            'utilizadores' => $utilizadores,
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }

    public function create(Request $request)
    {
        $roles = Role::all();

        activity()
            ->useLog('Utilizadores')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de utilizador.');

        return Inertia::render('Utilizadores/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telemovel' => 'nullable|string|max:20',
            'estado' => 'required|in:Ativo,Inativo',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telemovel' => $validated['telemovel'],
            'estado' => $validated['estado'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        activity()
            ->useLog('Utilizadores')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $user->name,
                'email' => $user->email,
                'estado' => $user->estado,
                'role' => $user->roles->pluck('name')->join(', '),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Criou o utilizador: {$user->name}.");

        return redirect()->route('utilizadores.index')->with('success', 'Utilizador criado com sucesso.');
    }

    public function edit(User $utilizador, Request $request)
    {
        $roles = Role::all();

        activity()
            ->useLog('Utilizadores')
            ->performedOn($utilizador)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $utilizador->name,
                'email' => $utilizador->email,
                'estado' => $utilizador->estado,
                'role' => $utilizador->roles->pluck('name')->join(', '),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Acedeu à edição do utilizador: {$utilizador->name}.");

        return Inertia::render('Utilizadores/Edit', [
            'utilizador' => $utilizador->load('roles'),
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $utilizador)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $utilizador->id,
            'telemovel' => 'nullable|string|max:20',
            'estado' => 'required|in:Ativo,Inativo',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $utilizador->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telemovel' => $validated['telemovel'],
            'estado' => $validated['estado'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $utilizador->password,
        ]);

        if (!$utilizador->hasRole($validated['role'])) {
            $utilizador->syncRoles([$validated['role']]);
        }

        activity()
            ->useLog('Utilizadores')
            ->performedOn($utilizador)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $utilizador->name,
                'email' => $utilizador->email,
                'estado' => $utilizador->estado,
                'role' => $utilizador->roles->pluck('name')->join(', '),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Atualizou o utilizador: {$utilizador->name}.");

        return redirect()->route('utilizadores.index')->with('success', 'Utilizador atualizado com sucesso.');
    }

    public function destroy(User $utilizador, Request $request)
    {
        $utilizador->delete();

        activity()
            ->useLog('Utilizadores')
            ->performedOn($utilizador)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $utilizador->name,
                'email' => $utilizador->email,
                'estado' => $utilizador->estado,
                'role' => $utilizador->roles->pluck('name')->join(', '),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Eliminou o utilizador: {$utilizador->name}.");

        return redirect()->route('utilizadores.index')->with('success', 'Utilizador eliminado com sucesso.');
    }

    public function show(User $utilizador, Request $request)
    {
        activity()
        ->useLog('Utilizadores')
        ->performedOn($utilizador)
        ->causedBy(auth()->user())
        ->withProperties([
            'nome' => $utilizador->name,
            'email' => $utilizador->email,
            'estado' => $utilizador->estado,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])
        ->log("Visualizou o utilizador: {$utilizador->name}.");

        return Inertia::render('Utilizadores/Show', [
            'utilizador' => $utilizador->load('roles'),
        ]);
    }
}
