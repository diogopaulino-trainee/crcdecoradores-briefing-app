<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use App\Models\Iva;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtigoController extends Controller
{
    public function index()
    {
        $artigos = Artigo::with('iva')->get(); // se tiveres relação com tabela de IVA
        return Inertia::render('Artigos/Index', [
            'artigos' => $artigos,
        ]);
    }

    public function create()
    {
        return Inertia::render('Artigos/Create', [
            'ivas' => Iva::all(), // para popular dropdown de IVA
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'referencia' => 'required|unique:artigos,referencia',
            'nome' => 'required',
            'preco' => 'required|numeric',
            'iva' => 'required|integer',
        ]);

        $artigo = Artigo::create($validated);

        activity()
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->log('Criou um artigo.');

        return redirect()->route('artigos.index')->with('success', 'Artigo criado com sucesso.');
    }

    public function edit(Artigo $artigo)
    {
        return Inertia::render('Artigos/Edit', [
            'artigo' => $artigo,
            'ivas' => Iva::all(),
        ]);
    }

    public function update(Request $request, Artigo $artigo)
    {
        $validated = $request->validate([
            'referencia' => 'required|unique:artigos,referencia,' . $artigo->id,
            'nome' => 'required',
            'preco' => 'required|numeric',
            'iva' => 'required|integer',
        ]);

        $artigo->update($validated);

        activity()
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->log('Atualizou o artigo.');

        return redirect()->route('artigos.index')->with('success', 'Artigo atualizado com sucesso.');
    }

    public function destroy(Artigo $artigo)
    {
        $artigo->delete();

        activity()
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->log('Eliminou o artigo.');

        return redirect()->route('artigos.index')->with('success', 'Artigo eliminado com sucesso.');
    }
}
