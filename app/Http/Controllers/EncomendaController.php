<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\Entidade;
use Illuminate\Http\Request;

class EncomendaController extends Controller
{
    public function index()
    {
        $encomendas = Encomenda::with('cliente')->get();
        return view('encomendas.index', compact('encomendas'));
    }

    public function create()
    {
        $clientes = Entidade::where('tipo', 'cliente')->get();
        return view('encomendas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|unique:encomendas,numero',
            'data_da_proposta' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
        ]);

        $encomenda = Encomenda::create($request->all());

        activity()
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->log('Criou uma encomenda.');

        return redirect()->route('encomendas.index')->with('success', 'Encomenda criada com sucesso.');
    }

    public function edit(Encomenda $encomenda)
    {
        $clientes = Entidade::where('tipo', 'cliente')->get();
        return view('encomendas.edit', compact('encomenda', 'clientes'));
    }

    public function update(Request $request, Encomenda $encomenda)
    {
        $request->validate([
            'numero' => 'required|unique:encomendas,numero,' . $encomenda->id,
            'data_da_proposta' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
        ]);

        $encomenda->update($request->all());

        activity()
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->log('Atualizou a encomenda.');

        return redirect()->route('encomendas.index')->with('success', 'Encomenda atualizada com sucesso.');
    }

    public function destroy(Encomenda $encomenda)
    {
        $encomenda->delete();

        activity()
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->log('Eliminou a encomenda.');

        return redirect()->route('encomendas.index')->with('success', 'Encomenda eliminada com sucesso.');
    }
}
