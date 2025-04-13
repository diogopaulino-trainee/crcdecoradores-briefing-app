<?php

namespace App\Http\Controllers;

use App\Models\Funcao;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FuncaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Funcao::query();

        if ($request->filled('termo')) {
            $query->where('nome', 'like', '%' . $request->termo . '%');
        }

        if ($request->filled('sort') && in_array($request->sort, ['nome'])) {
            $direction = $request->get('direction', 'asc') === 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort, $direction);
        } else {
            $query->orderBy('nome');
        }

        $funcoes = $query->paginate(10)->withQueryString();

        activity()
            ->useLog('Funções')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à listagem de funções.');

        return Inertia::render('Funcoes/Index', [
            'funcoes' => $funcoes,
            'filtros' => $request->only(['termo', 'sort', 'direction']),
        ]);
    }

    public function create(Request $request)
    {
        activity()
            ->useLog('Funções')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de função.');

        return Inertia::render('Funcoes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:funcoes,nome',
            'descricao' => 'required|string|max:1000',
        ], [
            'nome.unique' => 'Já existe uma função com esse nome.',
        ]);

        $funcao = Funcao::firstOrCreate(
            ['nome' => $validated['nome']],
            ['descricao' => $validated['descricao']]
        );

        if ($funcao->wasRecentlyCreated) {
            activity()
                ->useLog('Funções')
                ->performedOn($funcao)
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log('Criou a função: ' . $funcao->nome);
        }

        return redirect()->route('funcoes.index')->with('success', 'Função criada com sucesso.');
    }

    public function edit(Request $request, Funcao $funcao)
    {
        activity()
            ->useLog('Funções')
            ->performedOn($funcao)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à edição da função: ' . $funcao->nome);

        return Inertia::render('Funcoes/Edit', [
            'funcao' => $funcao,
        ]);
    }

    public function update(Request $request, Funcao $funcao)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:funcoes,nome,' . $funcao->id,
            'descricao' => 'required|string|max:1000',
        ], [
            'nome.unique' => 'Já existe uma função com esse nome.',
        ]);

        $funcao->update($validated);

        activity()
            ->useLog('Funções')
            ->performedOn($funcao)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Atualizou a função: ' . $funcao->nome);

        activity()
            ->performedOn($funcao)
            ->causedBy(auth()->user())
            ->log('Atualizou a função.');

        return redirect()->route('funcoes.index')->with('success', 'Função atualizada com sucesso.');
    }

    public function destroy(Funcao $funcao)
    {
        $nome = $funcao->nome;
        
        $funcao->delete();

        activity()
            ->useLog('Funções')
            ->performedOn($funcao)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Eliminou a função: ' . $nome);

        return redirect()->route('funcoes.index')->with('success', 'Função eliminada com sucesso.');
    }
}
