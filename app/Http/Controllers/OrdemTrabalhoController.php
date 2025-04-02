<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\OrdemTrabalho;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdemTrabalhoController extends Controller
{
    public function index()
    {
        $ordens = OrdemTrabalho::with('entidade')->get();

        return Inertia::render('OrdensTrabalho/Index', [
            'ordens' => $ordens,
        ]);
    }

    public function create()
    {
        $entidades = Entidade::all();

        return Inertia::render('OrdensTrabalho/Create', [
            'entidades' => $entidades,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:ordens_trabalho,numero',
            'data_da_ordem' => 'required|date',
            'entidade_id' => 'required|exists:entidades,id',
        ]);

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
