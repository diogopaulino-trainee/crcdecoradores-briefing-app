<?php

namespace App\Http\Controllers;

use App\Models\Iva;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IvaController extends Controller
{
    public function index(Request $request)
    {
        $query = Iva::query();

        if ($request->filled('termo')) {
            $query->where('nome', 'like', '%' . $request->termo . '%')
                ->orWhere('percentagem', 'like', '%' . $request->termo . '%');
        }

        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        $ivas = $query->paginate(10)->withQueryString();

        return Inertia::render('Ivas/Index', [
            'ivas' => $ivas,
            'filtros' => $request->only(['termo', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Ivas/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'percentagem' => 'required|numeric|unique:ivas,percentagem',
        ], [
            'percentagem.unique' => 'Já existe um IVA com essa percentagem.',
        ]);

        $iva = Iva::firstOrCreate(
            ['percentagem' => $validated['percentagem']],
            ['nome' => $validated['nome']]
        );

        activity()
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->log('Criou um registo de IVA.');

        return redirect()->route('ivas.index')->with('success', 'IVA criado com sucesso.');
    }

    public function edit(Iva $iva)
    {
        return Inertia::render('Ivas/Edit', [
            'iva' => $iva,
        ]);
    }

    public function update(Request $request, Iva $iva)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'percentagem' => 'required|numeric|unique:ivas,percentagem,' . $iva->id,
        ], [
            'percentagem.unique' => 'Já existe um IVA com essa percentagem.',
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
        if ($iva->artigos()->exists()) {
            return redirect()
                ->route('ivas.index')
                ->with('error', 'Não é possível eliminar o IVA, pois está a ser utilizado em artigos.');
        }

        $iva->delete();

        activity()
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->log('Eliminou o registo de IVA.');

        return redirect()
            ->route('ivas.index')
            ->with('success', 'IVA eliminado com sucesso.');
    }
}
