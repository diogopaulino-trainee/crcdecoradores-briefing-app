<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissaoController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::withCount('users')
            ->when($request->filled('termo'), fn ($q) =>
                $q->where('name', 'like', '%' . $request->termo . '%'))
            ->when($request->filled('estado'), fn ($q) =>
                $q->where('estado', $request->estado))
            ->orderBy($request->input('sort', 'name'), $request->input('direction', 'asc'))
            ->paginate(10)
            ->withQueryString();

        activity()
            ->useLog('Permissões')
            ->causedBy(auth()->user())
            ->withProperties([
                'termo' => $request->termo,
                'estado' => $request->estado,
                'sort' => $request->sort,
                'direction' => $request->direction,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à listagem de grupos de permissões.');

        return Inertia::render('Permissoes/Index', [
            'permissoes' => $roles,
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }

    public function create(Request $request)
    {
        $permissoesDisponiveis = Permission::all();

        activity()
            ->useLog('Permissões')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de grupo de permissões.');

        return Inertia::render('Permissoes/Create', [
            'permissoes' => $permissoesDisponiveis->groupBy(function ($p) {
                return explode('.', $p->name)[0];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,NULL,id,guard_name,web',
            'estado' => 'required|in:Ativo,Inativo',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'estado' => $validated['estado'],
        ]);

        if (!empty($validated['permissions'])) {
            $role->givePermissionTo($validated['permissions']);
        }

        activity()
            ->useLog('Permissões')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $role->name,
                'estado' => $role->estado,
                'permissoes' => $validated['permissions'] ?? [],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Criou o grupo de permissões \"{$role->name}\" com estado \"{$role->estado}\".");

        return to_route('permissoes.index')->with('success', 'Grupo criado com sucesso.');
    }

    public function edit(Request $request, Role $role)
    {
        $permissoesDisponiveis = Permission::all();

        activity()
            ->useLog('Permissões')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $role->name,
                'estado' => $role->estado,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Acedeu à edição do grupo de permissões \"{$role->name}\".");

        return Inertia::render('Permissoes/Edit', [
            'grupo' => $role->load('permissions'),
            'permissoes' => $permissoesDisponiveis->groupBy(function ($p) {
                return explode('.', $p->name)[0];
            }),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'estado' => 'required|in:Ativo,Inativo',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update([
            'name' => $validated['name'],
            'estado' => $validated['estado'],
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionIds = Permission::query()
        ->whereIn('name', $validated['permissions'] ?? [])
        ->pluck('id')
        ->unique()
        ->values()
        ->all();

        $role->syncPermissions($permissionIds);

        activity()
            ->useLog('Permissões')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $validated['name'],
                'estado' => $validated['estado'],
                'permissoes' => $validated['permissions'] ?? [],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Atualizou o grupo de permissões para \"{$validated['name']}\" com estado \"{$validated['estado']}\".");

        return redirect()->route('permissoes.index')->with('success', 'Grupo atualizado com sucesso.');
    }

    public function destroy(Request $request, Role $role)
    {
        $nome = $role->name;
        $role->delete();

        activity()
            ->useLog('Permissões')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $nome,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Eliminou o grupo de permissões \"{$nome}\".");

        return redirect()->route('permissoes.index')->with('success', 'Grupo eliminado com sucesso.');
    }
}
