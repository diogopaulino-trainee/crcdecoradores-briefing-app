<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\OrdemTrabalho;
use Illuminate\Http\Request;

class OrdemTrabalhoController extends Controller
{
    public function index()
    {
        $ordens = OrdemTrabalho::with('entidade')->get();
        return view('ordens_trabalho.index', compact('ordens'));
    }

    public function create()
    {
        $entidades = Entidade::all();
        return view('ordens_trabalho.create', compact('entidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|unique:ordens_trabalho,numero',
            'data_da_ordem' => 'required|date',
            'entidade_id' => 'required|exists:entidades,id',
        ]);

        $ordem = OrdemTrabalho::create($request->all());

        activity()
            ->performedOn($ordem)
            ->causedBy(auth()->user())
            ->log('Criou uma ordem de trabalho.');

        return redirect()->route('ordens_trabalho.index')->with('success', 'Ordem de trabalho criada com sucesso.');
    }

    public function edit(OrdemTrabalho $ordemTrabalho)
    {
        $entidades = Entidade::all();
        return view('ordens_trabalho.edit', compact('ordemTrabalho', 'entidades'));
    }

    public function update(Request $request, OrdemTrabalho $ordemTrabalho)
    {
        $request->validate([
            'numero' => 'required|unique:ordens_trabalho,numero,' . $ordemTrabalho->id,
            'data_da_ordem' => 'required|date',
            'entidade_id' => 'required|exists:entidades,id',
        ]);

        $ordemTrabalho->update($request->all());

        activity()
            ->performedOn($ordemTrabalho)
            ->causedBy(auth()->user())
            ->log('Atualizou a ordem de trabalho.');

        return redirect()->route('ordens_trabalho.index')->with('success', 'Ordem de trabalho atualizada com sucesso.');
    }

    public function destroy(OrdemTrabalho $ordemTrabalho)
    {
        $ordemTrabalho->delete();

        activity()
            ->performedOn($ordemTrabalho)
            ->causedBy(auth()->user())
            ->log('Eliminou a ordem de trabalho.');

        return redirect()->route('ordens_trabalho.index')->with('success', 'Ordem de trabalho eliminada com sucesso.');
    }
}
