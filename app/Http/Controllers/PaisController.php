<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
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

        activity()
            ->useLog('Países')
            ->causedBy(auth()->user())
            ->withProperties([
                'termo' => $request->termo,
                'sort' => $request->sort,
                'direction' => $request->direction,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à listagem dos países.');

        return Inertia::render('Paises/Index', [
            'paises' => $paises,
            'filtros' => $request->only(['termo', 'sort', 'direction']),
        ]);
    }

    public function create(Request $request)
    {
        activity()
            ->useLog('Países')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de país.');

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
            ->useLog('Países')
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $pais->nome,
                'codigo' => $pais->codigo,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Criou o país \"{$pais->nome}\" com o código \"{$pais->codigo}\".");

        return redirect()->route('paises.index')->with('success', 'País criado com sucesso.');
    }

    public function edit(Request $request, Pais $pais)
    {
        activity()
            ->useLog('Países')
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $pais->nome,
                'codigo' => $pais->codigo,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Acedeu à edição do país \"{$pais->nome}\".");

        return Inertia::render('Paises/Edit', [
            'pais' => $pais,
        ]);
    }

    public function update(Request $request, Pais $pais)
    {
        $validated = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('paises', 'nome')->ignore($pais->id, 'id'),
            ],
            'codigo' => [
                'required',
                'string',
                'max:5',
                Rule::unique('paises', 'codigo')->ignore($pais->id, 'id'),
            ],
        ], [
            'nome.unique' => 'Já existe um país com esse nome.',
            'codigo.unique' => 'Já existe um país com esse código.',
        ]);

        $pais->update($validated);

        activity()
            ->useLog('Países')
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $pais->nome,
                'codigo' => $pais->codigo,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Atualizou o país para \"{$pais->nome}\" com o código \"{$pais->codigo}\".");

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
            ->useLog('Países')
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $pais->nome,
                'codigo' => $pais->codigo,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Eliminou o país \"{$pais->nome}\" com o código \"{$pais->codigo}\".");

        return redirect()->route('paises.index')->with('success', 'País eliminado com sucesso.');
    }
}
