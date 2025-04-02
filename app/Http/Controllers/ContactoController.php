<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactoController extends Controller
{
    public function index()
    {
        $contactos = Contacto::with('entidade')->get();

        return Inertia::render('Contactos/Index', [
            'contactos' => $contactos,
        ]);
    }

    public function create()
    {
        return Inertia::render('Contactos/Create', [
            'entidades' => Entidade::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'primeiro_nome' => 'required',
            'apelido' => 'required',
            'entidade_id' => 'required|exists:entidades,id',
        ]);

        $contacto = Contacto::create($validated);

        activity()
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->log('Criou um contacto.');

        return redirect()->route('contactos.index')->with('success', 'Contacto criado com sucesso.');
    }

    public function edit(Contacto $contacto)
    {
        return Inertia::render('Contactos/Edit', [
            'contacto' => $contacto,
            'entidades' => Entidade::all(),
        ]);
    }

    public function update(Request $request, Contacto $contacto)
    {
        $validated = $request->validate([
            'primeiro_nome' => 'required',
            'apelido' => 'required',
            'entidade_id' => 'required|exists:entidades,id',
        ]);

        $contacto->update($validated);

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
