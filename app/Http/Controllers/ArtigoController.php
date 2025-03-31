<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use Illuminate\Http\Request;

class ArtigoController extends Controller
{
    public function index()
    {
        $artigos = Artigo::all();
        return view('artigos.index', compact('artigos'));
    }

    public function create()
    {
        return view('artigos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'referencia' => 'required|unique:artigos,referencia',
            'nome' => 'required',
            'preco' => 'required|numeric',
            'iva' => 'required|integer',
        ]);

        $artigo = Artigo::create($request->all());

        activity()
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->log('Criou um artigo.');

        return redirect()->route('artigos.index')->with('success', 'Artigo criado com sucesso.');
    }

    public function edit(Artigo $artigo)
    {
        return view('artigos.edit', compact('artigo'));
    }

    public function update(Request $request, Artigo $artigo)
    {
        $request->validate([
            'referencia' => 'required|unique:artigos,referencia,' . $artigo->id,
            'nome' => 'required',
            'preco' => 'required|numeric',
            'iva' => 'required|integer',
        ]);

        $artigo->update($request->all());

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
