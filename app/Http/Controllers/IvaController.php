<?php

namespace App\Http\Controllers;

use App\Models\Iva;
use Illuminate\Http\Request;

class IvaController extends Controller
{
    public function index()
    {
        $ivas = Iva::all();
        return view('ivas.index', compact('ivas'));
    }

    public function create()
    {
        return view('ivas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'percentagem' => 'required|numeric',
        ]);

        $iva = Iva::create($request->all());

        activity()
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->log('Criou um registo de IVA.');

        return redirect()->route('ivas.index')->with('success', 'IVA criado com sucesso.');
    }

    public function edit(Iva $iva)
    {
        return view('ivas.edit', compact('iva'));
    }

    public function update(Request $request, Iva $iva)
    {
        $request->validate([
            'nome' => 'required',
            'percentagem' => 'required|numeric',
        ]);

        $iva->update($request->all());

        activity()
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->log('Atualizou o registo de IVA.');

        return redirect()->route('ivas.index')->with('success', 'IVA atualizado com sucesso.');
    }

    public function destroy(Iva $iva)
    {
        $iva->delete();

        activity()
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->log('Eliminou o registo de IVA.');

        return redirect()->route('ivas.index')->with('success', 'IVA eliminado com sucesso.');
    }
}
