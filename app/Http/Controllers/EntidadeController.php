<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Illuminate\Http\Request;

class EntidadeController extends Controller
{
    public function index()
    {
        $entidades = Entidade::all();
        return view('entidades.index', compact('entidades'));
    }

    public function create()
    {
        return view('entidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nif' => 'required|unique:entidades,nif',
            'nome' => 'required',
        ]);

        $entidade = Entidade::create($request->all());

        activity()
            ->performedOn($entidade)
            ->causedBy(auth()->user())
            ->withProperties(['tipo' => $entidade->tipo])
            ->log('Criou uma entidade.');

        return redirect()->route('entidades.index')->with('success', 'Entidade criada com sucesso.');
    }

    public function edit(Entidade $entidade)
    {
        return view('entidades.edit', compact('entidade'));
    }

    public function update(Request $request, Entidade $entidade)
    {
        $request->validate([
            'nif' => 'required|unique:entidades,nif,' . $entidade->id,
            'nome' => 'required',
        ]);

        $entidade->update($request->all());

        activity()
            ->performedOn($entidade)
            ->causedBy(auth()->user())
            ->log('Atualizou a entidade.');

        return redirect()->route('entidades.index')->with('success', 'Entidade atualizada com sucesso.');
    }

    public function destroy(Entidade $entidade)
    {
        $entidade->delete();

        activity()
            ->performedOn($entidade)
            ->causedBy(auth()->user())
            ->log('Eliminou a entidade.');

        return redirect()->route('entidades.index')->with('success', 'Entidade eliminada com sucesso.');
    }
}
