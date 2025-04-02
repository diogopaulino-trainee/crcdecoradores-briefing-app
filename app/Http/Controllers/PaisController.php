<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaisController extends Controller
{
    public function index()
    {
        $paises = Pais::all();

        return Inertia::render('Paises/Index', [
            'paises' => $paises,
        ]);
    }

    public function create()
    {
        return Inertia::render('Paises/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required',
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
            'nome' => 'required',
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
        $pais->delete();

        activity()
            ->performedOn($pais)
            ->causedBy(auth()->user())
            ->log('Eliminou o país.');

        return redirect()->route('paises.index')->with('success', 'País eliminado com sucesso.');
    }
}
