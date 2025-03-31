<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\Proposta;
use Illuminate\Http\Request;

class PropostaController extends Controller
{
    public function index()
    {
        $propostas = Proposta::with('cliente')->get();
        return view('propostas.index', compact('propostas'));
    }

    public function create()
    {
        // Apenas entidades do tipo 'cliente'
        $clientes = Entidade::where('tipo', 'cliente')->get();
        return view('propostas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|unique:propostas,numero',
            'data_da_proposta' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
            'validade' => 'required|date',
        ]);

        $proposta = Proposta::create($request->all());

        activity()
            ->performedOn($proposta)
            ->causedBy(auth()->user())
            ->log('Criou uma proposta.');

        return redirect()->route('propostas.index')->with('success', 'Proposta criada com sucesso.');
    }

    public function edit(Proposta $proposta)
    {
        $clientes = Entidade::where('tipo', 'cliente')->get();
        return view('propostas.edit', compact('proposta', 'clientes'));
    }

    public function update(Request $request, Proposta $proposta)
    {
        $request->validate([
            'numero' => 'required|unique:propostas,numero,' . $proposta->id,
            'data_da_proposta' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
            'validade' => 'required|date',
        ]);

        $proposta->update($request->all());

        activity()
            ->performedOn($proposta)
            ->causedBy(auth()->user())
            ->log('Atualizou a proposta.');

        return redirect()->route('propostas.index')->with('success', 'Proposta atualizada com sucesso.');
    }

    public function destroy(Proposta $proposta)
    {
        $proposta->delete();

        activity()
            ->performedOn($proposta)
            ->causedBy(auth()->user())
            ->log('Eliminou a proposta.');

        return redirect()->route('propostas.index')->with('success', 'Proposta eliminada com sucesso.');
    }
}
