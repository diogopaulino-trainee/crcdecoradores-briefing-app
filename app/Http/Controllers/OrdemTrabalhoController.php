<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\OrdemTrabalho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class OrdemTrabalhoController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdemTrabalho::with('entidade');

        if ($request->filled('termo')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero', 'like', '%' . (string) $request->termo . '%')
                ->orWhereHas('entidade', function ($subquery) use ($request) {
                    $subquery->where('nome', 'like', '%' . $request->termo . '%');
                });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $sort = $request->input('sort', 'data_da_ordem');
        $direction = $request->input('direction', 'desc');

        $query->orderBy($sort, $direction);

        $ordens = $query->paginate(10)->withQueryString();

        activity()
            ->useLog('Ordens de Trabalho')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à listagem das ordens de trabalho.');

        return Inertia::render('OrdensTrabalho/Index', [
            'ordens' => $ordens,
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }

    public function show(Request $request, OrdemTrabalho $ordemTrabalho)
    {
        $ordemTrabalho->load('entidade');

        activity()
        ->useLog('Ordens de Trabalho')
        ->performedOn($ordemTrabalho)
        ->causedBy(auth()->user())
        ->withProperties([
            'numero' => $ordemTrabalho->numero,
            'entidade' => $ordemTrabalho->entidade?->nome,
            'estado' => $ordemTrabalho->estado,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])
        ->log("Acedeu à visualização da ordem de trabalho nº {$ordemTrabalho->numero} ({$ordemTrabalho->estado}) da entidade \"{$ordemTrabalho->entidade?->nome}\".");


        return Inertia::render('OrdensTrabalho/Show', [
        'ordemTrabalho' => $ordemTrabalho,
    ]);
    }

    public function create(Request $request)
    {
        $proximoNumero = (OrdemTrabalho::max('numero') ?? 0) + 1;

        activity()
        ->useLog('Ordens de Trabalho')
        ->causedBy(auth()->user())
        ->withProperties([
            'proximo_numero' => $proximoNumero,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])
        ->log("Acedeu ao formulário de criação de nova ordem de trabalho (nº sugerido: {$proximoNumero}).");

        return Inertia::render('OrdensTrabalho/Create', [
            'entidades' => Entidade::all(),
            'proximoNumero' => $proximoNumero,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_da_ordem' => 'required|date',
            'entidade_id' => 'required|exists:entidades,id',
            'descricao' => 'nullable|string',
            'estado' => ['required', 'in:Pendente,Em Execução,Concluída,Cancelada'],
        ]);

        $validated['numero'] = (OrdemTrabalho::max('numero') ?? 0) + 1;

        DB::beginTransaction();
        try {
            $ordem = OrdemTrabalho::create($validated);

            activity()
                ->useLog('Ordens de Trabalho')
                ->performedOn($ordem)
                ->causedBy(auth()->user())
                ->withProperties([
                    'numero' => $ordem->numero,
                    'entidade_id' => $ordem->entidade_id,
                    'estado' => $ordem->estado,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log("Criou a ordem de trabalho nº {$ordem->numero} para a entidade ID {$ordem->entidade_id} com estado \"{$ordem->estado}\".");

            DB::commit();

            return redirect()->route('ordens-trabalho.index')->with('success', 'Ordem de trabalho criada com sucesso.');
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->withErrors('Erro ao criar a ordem de trabalho.')->withInput();
        }
    }

    public function edit(Request $request, OrdemTrabalho $ordemTrabalho)
    {
        $entidades = Entidade::all();
        $ordemTrabalho->load('entidade');

        activity()
        ->useLog('Ordens de Trabalho')
        ->performedOn($ordemTrabalho)
        ->causedBy(auth()->user())
        ->withProperties([
            'numero' => $ordemTrabalho->numero,
            'entidade' => $ordemTrabalho->entidade?->nome,
            'estado' => $ordemTrabalho->estado,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])
        ->log("Acedeu à edição da ordem de trabalho nº {$ordemTrabalho->numero} ({$ordemTrabalho->estado}) da entidade \"{$ordemTrabalho->entidade?->nome}\".");

        return Inertia::render('OrdensTrabalho/Edit', [
            'ordemTrabalho' => $ordemTrabalho,
            'entidades' => $entidades,
        ]);
    }

    public function update(Request $request, OrdemTrabalho $ordemTrabalho)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:ordens_trabalho,numero,' . $ordemTrabalho->id,
            'data_da_ordem' => 'required|date',
            'entidade_id' => 'required|exists:entidades,id',
            'estado' => ['required', 'in:Pendente,Em Execução,Concluída,Cancelada'],
            'descricao' => ['nullable', 'string'],
        ]);

        $ordemTrabalho->update($validated);

        activity()
        ->useLog('Ordens de Trabalho')
        ->performedOn($ordemTrabalho)
        ->causedBy(auth()->user())
        ->withProperties([
            'numero' => $ordemTrabalho->numero,
            'entidade_id' => $ordemTrabalho->entidade_id,
            'estado' => $ordemTrabalho->estado,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])
        ->log("Atualizou a ordem de trabalho nº {$ordemTrabalho->numero} para entidade ID {$ordemTrabalho->entidade_id}, novo estado: \"{$ordemTrabalho->estado}\".");

        return redirect()->route('ordens-trabalho.index')->with('success', 'Ordem de trabalho atualizada com sucesso.');
    }

    public function destroy(Request $request, OrdemTrabalho $ordemTrabalho)
    {
        $ordemTrabalho->delete();

        activity()
        ->useLog('Ordens de Trabalho')
        ->performedOn($ordemTrabalho)
        ->causedBy(auth()->user())
        ->withProperties([
            'numero' => $ordemTrabalho->numero,
            'entidade_id' => $ordemTrabalho->entidade_id,
            'estado' => $ordemTrabalho->estado,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])
        ->log("Eliminou a ordem de trabalho nº {$ordemTrabalho->numero} da entidade ID {$ordemTrabalho->entidade_id} com estado \"{$ordemTrabalho->estado}\".");

        return redirect()->route('ordens-trabalho.index')->with('success', 'Ordem de trabalho eliminada com sucesso.');
    }
}
