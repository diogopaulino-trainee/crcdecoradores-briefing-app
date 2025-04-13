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

        activity()
            ->useLog('IVAs')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Listou os registos de IVA.');

        return Inertia::render('Ivas/Index', [
            'ivas' => $ivas,
            'filtros' => $request->only(['termo', 'sort', 'direction']),
        ]);
    }

    public function create(Request $request)
    {
        activity()
            ->useLog('IVAs')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de IVA.');

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
            ->useLog('IVAs')
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $iva->nome,
                'percentagem' => $iva->percentagem,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Criou o IVA: {$iva->nome} ({$iva->percentagem}%).");

        return redirect()->route('ivas.index')->with('success', 'IVA criado com sucesso.');
    }

    public function edit(Request $request, Iva $iva)
    {
        activity()
            ->useLog('IVAs')
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $iva->nome,
                'percentagem' => $iva->percentagem,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Acedeu à edição do IVA: {$iva->nome} ({$iva->percentagem}%).");

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
            ->useLog('IVAs')
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $iva->nome,
                'percentagem' => $iva->percentagem,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("Atualizou o IVA: {$iva->nome} ({$iva->percentagem}%).");

        return redirect()->route('ivas.index')->with('success', 'IVA atualizado com sucesso.');
    }

    public function destroy(Iva $iva)
    {
        if ($iva->artigos()->exists()) {
            return redirect()
                ->route('ivas.index')
                ->with('error', 'Não é possível eliminar o IVA, pois está a ser utilizado em artigos.');
        }

        $nome = $iva->nome;
        $percentagem = $iva->percentagem;

        $iva->delete();

        activity()
            ->useLog('IVAs')
            ->performedOn($iva)
            ->causedBy(auth()->user())
            ->withProperties([
                'nome' => $nome,
                'percentagem' => $percentagem,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Eliminou o IVA: {$nome} ({$percentagem}%).");

        return redirect()
            ->route('ivas.index')
            ->with('success', 'IVA eliminado com sucesso.');
    }
}
