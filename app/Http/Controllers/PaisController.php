<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function index()
    {
        $paises = Pais::all();
        return view('paises.index', compact('paises'));
    }

    public function create()
    {
        return view('paises.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
        ]);

        $pais = Pais::create($request->all());

        activity()
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->log('Criou um país.');

        return redirect()->route('paises.index')->with('success', 'País criado com sucesso.');
    }

    public function edit(Pais $pais)
    {
        return view('paises.edit', compact('pais'));
    }

    public function update(Request $request, Pais $pais)
    {
        $request->validate([
            'nome' => 'required',
        ]);

        $pais->update($request->all());

        activity()
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->log('Atualizou o país.');

        return redirect()->route('paises.index')->with('success', 'País atualizado com sucesso.');
    }

    public function destroy(Pais $pais)
    {
        $pais->delete();

        activity()
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->log('Eliminou o país.');

        return redirect()->route('paises.index')->with('success', 'País eliminado com sucesso.');
    }
}
