<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaisController extends Controller
{
    public function index(Request $request)
    {
        $query = Pais::query();

        if ($request->filled('termo')) {
            $query->where(function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->termo . '%')
                ->orWhere('codigo', 'like', '%' . $request->termo . '%');
            });
        }

        if ($request->filled('sort') && in_array($request->sort, ['nome', 'codigo'])) {
            $direction = $request->get('direction', 'asc') === 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort, $direction);
        } else {
            $query->orderBy('nome');
        }

        $paises = $query->paginate(10)->withQueryString();

        return Inertia::render('Paises/Index', [
            'paises' => $paises,
            'filtros' => $request->only(['termo', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Paises/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:paises,nome',
            'codigo' => 'required|string|max:5|unique:paises,codigo',
        ], [
            'nome.unique' => 'Já existe um país com esse nome.',
            'codigo.unique' => 'Já existe um país com esse código.',
        ]);

        $pais = Pais::create($validated);

        activity()
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->log('Criou um país.');

        return redirect()->route('paises.index')->with('success', 'País criado com sucesso.');
    }

    public function edit(Pais $pais)
    {
        return Inertia::render('Paises/Edit', [
            'pais' => $pais,
        ]);
    }

    public function update(Request $request, Pais $pais)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:paises,nome,' . $pais->id,
            'codigo' => 'required|string|max:5|unique:paises,codigo,' . $pais->id,
        ], [
            'nome.unique' => 'Já existe um país com esse nome.',
            'codigo.unique' => 'Já existe um país com esse código.',
        ]);

        $pais->update($validated);

        activity()
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->log('Atualizou o país.');

        return redirect()->route('paises.index')->with('success', 'País atualizado com sucesso.');
    }

    public function destroy(Pais $pais)
    {
        if ($pais->entidades()->exists()) {
            return redirect()->route('paises.index')
                ->with('error', 'Não é possível eliminar o país porque tem entidades associadas.');
        }

        Log::info('A eliminar país ID: ' . $pais->id);
        $pais->delete();

        activity()
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->log('Eliminou o país.');

        return redirect()->route('paises.index')->with('success', 'País eliminado com sucesso.');
    }
}
