<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function index()
    {
        $contactos = Contacto::with('entidade')->get();
        return view('contactos.index', compact('contactos'));
    }

    public function create()
    {
        $entidades = Entidade::all();
        return view('contactos.create', compact('entidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'primeiro_nome' => 'required',
            'apelido' => 'required',
            'entidade_id' => 'required|exists:entidades,id',
        ]);

        $contacto = Contacto::create($request->all());

        activity()
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->log('Criou um contacto.');

        return redirect()->route('contactos.index')->with('success', 'Contacto criado com sucesso.');
    }

    public function edit(Contacto $contacto)
    {
        $entidades = Entidade::all();
        return view('contactos.edit', compact('contacto', 'entidades'));
    }

    public function update(Request $request, Contacto $contacto)
    {
        $request->validate([
            'primeiro_nome' => 'required',
            'apelido' => 'required',
            'entidade_id' => 'required|exists:entidades,id',
        ]);

        $contacto->update($request->all());

        activity()
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->log('Atualizou o contacto.');

        return redirect()->route('contactos.index')->with('success', 'Contacto atualizado com sucesso.');
    }

    public function destroy(Contacto $contacto)
    {
        $contacto->delete();

        activity()
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->log('Eliminou o contacto.');

        return redirect()->route('contactos.index')->with('success', 'Contacto eliminado com sucesso.');
    }
}
