<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\OrdemTrabalho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrdemTrabalhoController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdemTrabalho::with('entidade');

        if ($request->filled('termo')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero', 'like', '%' . (string) $request->termo . '%')
                ->orWhereHas('entidade', function ($subquery) use ($request) {
                    $subquery->where('nome', 'like', '%' . $request->termo . '%');
                });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $sort = $request->input('sort', 'data_da_ordem');
        $direction = $request->input('direction', 'desc');

        $query->orderBy($sort, $direction);

        $ordens = $query->paginate(10)->withQueryString();

        return Inertia::render('OrdensTrabalho/Index', [
            'ordens' => $ordens,
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }

    public function show(OrdemTrabalho $ordemTrabalho)
    {
        return Inertia::render('OrdensTrabalho/Show', [
            'ordemTrabalho' => $ordemTrabalho->load('entidade'),
        ]);
    }

    public function create()
    {
        $proximoNumero = (OrdemTrabalho::max('numero') ?? 0) + 1;

        return Inertia::render('OrdensTrabalho/Create', [
            'entidades' => Entidade::all(),
            'proximoNumero' => $proximoNumero,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_da_ordem' => 'required|date',
            'entidade_id' => 'required|exists:entidades,id',
            'descricao' => 'nullable|string',
            'estado' => ['required', 'in:Pendente,Em Execução,Concluída,Cancelada'],
        ]);

        $validated['numero'] = (OrdemTrabalho::max('numero') ?? 0) + 1;

        $ordem = OrdemTrabalho::create($validated);

        activity()
            ->performedOn($ordem)
            ->causedBy(auth()->user())
            ->log('Criou uma ordem de trabalho.');

        return redirect()->route('ordens-trabalho.index')->with('success', 'Ordem de trabalho criada com sucesso.');
    }

    public function edit(OrdemTrabalho $ordemTrabalho)
    {
        $entidades = Entidade::all();
        $ordemTrabalho->load('entidade');

        return Inertia::render('OrdensTrabalho/Edit', [
            'ordemTrabalho' => $ordemTrabalho,
            'entidades' => $entidades,
        ]);
    }

    public function update(Request $request, OrdemTrabalho $ordemTrabalho)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:ordens_trabalho,numero,' . $ordemTrabalho->id,
            'data_da_ordem' => 'required|date',
            'entidade_id' => 'required|exists:entidades,id',
            'estado' => ['required', 'in:Pendente,Em Execução,Concluída,Cancelada'],
            'descricao' => ['nullable', 'string'],
        ]);

        $ordemTrabalho->update($validated);

        activity()
            ->performedOn($ordemTrabalho)
            ->causedBy(auth()->user())
            ->log('Atualizou a ordem de trabalho.');

        return redirect()->route('ordens-trabalho.index')->with('success', 'Ordem de trabalho atualizada com sucesso.');
    }

    public function destroy(OrdemTrabalho $ordemTrabalho)
    {
        $ordemTrabalho->delete();

        activity()
            ->performedOn($ordemTrabalho)
            ->causedBy(auth()->user())
            ->log('Eliminou a ordem de trabalho.');

        return redirect()->route('ordens-trabalho.index')->with('success', 'Ordem de trabalho eliminada com sucesso.');
    }
}
