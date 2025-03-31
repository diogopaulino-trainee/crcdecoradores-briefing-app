<?php

namespace App\Http\Controllers;

use App\Models\Funcao;
use Illuminate\Http\Request;

class FuncaoController extends Controller
{
    public function index()
    {
        $funcoes = Funcao::all();
        return view('funcoes.index', compact('funcoes'));
    }

    public function create()
    {
        return view('funcoes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
        ]);

        $funcao = Funcao::create($request->all());

        activity()
            ->performedOn($funcao)
            ->causedBy(auth()->user())
            ->log('Criou uma função.');

        return redirect()->route('funcoes.index')->with('success', 'Função criada com sucesso.');
    }

    public function edit(Funcao $funcao)
    {
        return view('funcoes.edit', compact('funcao'));
    }

    public function update(Request $request, Funcao $funcao)
    {
        $request->validate([
            'nome' => 'required',
        ]);

        $funcao->update($request->all());

        activity()
            ->performedOn($funcao)
            ->causedBy(auth()->user())
            ->log('Atualizou a função.');

        return redirect()->route('funcoes.index')->with('success', 'Função atualizada com sucesso.');
    }

    public function destroy(Funcao $funcao)
    {
        $funcao->delete();

        activity()
            ->performedOn($funcao)
            ->causedBy(auth()->user())
            ->log('Eliminou a função.');

        return redirect()->route('funcoes.index')->with('success', 'Função eliminada com sucesso.');
    }
}
