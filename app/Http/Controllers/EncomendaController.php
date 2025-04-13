<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use App\Models\Empresa;
use App\Models\Encomenda;
use App\Models\Entidade;
use App\Models\Proposta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class EncomendaController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'data_da_proposta');
        $direction = $request->input('direction', 'desc');

        $encomendas = Encomenda::with('cliente')
            ->when($request->input('termo'), function ($query, $termo) {
                $query->where('numero', 'like', "%$termo%")
                    ->orWhereHas('cliente', fn($q) => $q->where('nome', 'like', "%$termo%"));
            })
            ->when($request->input('estado'), fn($q, $estado) => $q->where('estado', $estado))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        activity()
            ->useLog('Encomendas')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'filtros' => $request->all(),
            ])
            ->log('Acedeu à listagem de encomendas (' . ($request->route()->getName() ?? 'todas') . ').');

        return Inertia::render('Encomendas/Index', [
            'encomendas' => $encomendas,
            'filtro' => 'todas',
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }

    public function clientes(Request $request)
    {
        $sort = $request->input('sort', 'data_da_proposta');
        $direction = $request->input('direction', 'desc');

        $encomendas = Encomenda::with('cliente')
            ->where('tipo', 'cliente')
            ->whereHas('cliente', fn ($q) => $q->where('tipo', 'cliente'))
            ->when($request->input('termo'), function ($query, $termo) {
                $query->where('numero', 'like', "%$termo%")
                    ->orWhereHas('cliente', fn($q) => $q->where('nome', 'like', "%$termo%"));
            })
            ->when($request->input('estado'), fn($q, $estado) => $q->where('estado', $estado))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        activity()
            ->useLog('Encomendas')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'filtros' => $request->all(),
            ])
            ->log('Acedeu à listagem de encomendas (' . ($request->route()->getName() ?? 'todas') . ').');

        return Inertia::render('Encomendas/Index', [
            'encomendas' => $encomendas,
            'filtro' => 'clientes',
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }

    public function fornecedores(Request $request)
    {
        $sort = $request->input('sort', 'data_da_proposta');
        $direction = $request->input('direction', 'desc');

        $encomendas = Encomenda::with('cliente')
            ->where('tipo', 'fornecedor')
            ->whereHas('cliente', fn ($q) => $q->where('tipo', 'fornecedor'))
            ->when($request->input('termo'), function ($query, $termo) {
                $query->where('numero', 'like', "%$termo%")
                    ->orWhereHas('cliente', fn($q) => $q->where('nome', 'like', "%$termo%"));
            })
            ->when($request->input('estado'), fn($q, $estado) => $q->where('estado', $estado))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        activity()
            ->useLog('Encomendas')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'filtros' => $request->all(),
            ])
            ->log('Acedeu à listagem de encomendas (' . ($request->route()->getName() ?? 'todas') . ').');

        return Inertia::render('Encomendas/Index', [
            'encomendas' => $encomendas,
            'filtro' => 'fornecedores',
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }

    public function create(Request $request)
    {
        $tipo = $request->query('tipo');

        $clientes = Entidade::where('tipo', 'cliente')->get();
        $fornecedores = Entidade::where('tipo', 'fornecedor')->get();
        $proximoNumero = (Encomenda::max('numero') ?? 2000) + 1;

        activity()
            ->useLog('Encomendas')
            ->causedBy(auth()->user())
            ->withProperties([
                'tipo' => $request->query('tipo'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de encomenda (' . ucfirst($request->query('tipo', 'cliente')) . ').');

        return Inertia::render('Encomendas/Create', [
            'tipo' => $tipo,
            'clientes' => $clientes,
            'fornecedores' => $fornecedores,
            'artigos' => Artigo::all(),
            'proximoNumero' => $proximoNumero,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:entidades,id',
            'estado' => 'required|in:Rascunho,Fechado',
            'data_da_proposta' => $request->estado === 'Fechado' ? 'required|date' : 'nullable|date',
            'validade' => 'nullable|date',
            'linhas' => 'required|array|min:1',
            'linhas.*.artigo_id' => 'required|exists:artigos,id',
            'linhas.*.fornecedor_id' => 'nullable|exists:entidades,id',
            'linhas.*.quantidade' => 'required|numeric|min:1',
            'linhas.*.preco_unitario' => 'required|numeric|min:0',
        ]);

        if ($validated['estado'] === 'Fechado' && !$request->filled('data_da_proposta')) {
            $validated['data_da_proposta'] = now();
        }

        DB::beginTransaction();
        try {
            $ultimoNumero = DB::table('encomendas')->lockForUpdate()->max('numero');
            $validated['numero'] = ($ultimoNumero ?? 2000) + 1;

            $validated['tipo'] = Entidade::findOrFail($validated['cliente_id'])->tipo;

            $encomenda = Encomenda::create($validated);

            $total = 0;
            foreach ($validated['linhas'] as $linha) {
                $encomenda->linhas()->create($linha);
                $total += $linha['quantidade'] * $linha['preco_unitario'];
            }

            $encomenda->update(['valor_total' => $total]);

            DB::commit();

            activity()
                ->useLog('Encomendas')
                ->performedOn($encomenda)
                ->causedBy(auth()->user())
                ->withProperties([
                    'numero' => $encomenda->numero,
                    'tipo' => $encomenda->tipo,
                    'valor_total' => $encomenda->valor_total,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log('Criou a encomenda Nº ' . $encomenda->numero . ' para ' . optional($encomenda->cliente)->nome . ' (' . ucfirst($encomenda->tipo) . ').');

            return $this->redirecionarParaIndexPorTipo($encomenda)
                ->with('success', 'Encomenda criada com sucesso.');
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->withErrors('Erro ao criar a encomenda.')->withInput();
        }
    }

    public function edit(Encomenda $encomenda)
    {
        $encomenda->load('linhas.artigo.iva', 'cliente');

        if ($encomenda->tipo === 'cliente') {
            $clientes = Entidade::where('tipo', 'cliente')->get();
        } elseif ($encomenda->tipo === 'fornecedor') {
            $clientes = Entidade::where('tipo', 'fornecedor')->get();
        } else {
            $clientes = collect();
        }

        if ($encomenda->cliente && !$clientes->contains('id', $encomenda->cliente->id)) {
            $clientes->push($encomenda->cliente);
        }

        activity()
            ->useLog('Encomendas')
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->withProperties([
                'numero' => $encomenda->numero,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Acedeu à edição da encomenda Nº ' . $encomenda->numero . ' de ' . optional($encomenda->cliente)->nome . ' (' . ucfirst($encomenda->tipo) . ').');

        return Inertia::render('Encomendas/Edit', [
            'encomenda' => $encomenda,
            'clientes' => $clientes,
            'fornecedores' => Entidade::where('tipo', 'fornecedor')->get(),
            'artigos' => Artigo::all(),
        ]);
    }

    public function update(Request $request, Encomenda $encomenda)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:encomendas,numero,' . $encomenda->id,
            'cliente_id' => 'required|exists:entidades,id',
            'estado' => 'required|in:Rascunho,Fechado',
            'data_da_proposta' => $request->estado === 'Fechado' ? 'required|date' : 'nullable|date',
            'validade' => 'nullable|date',
            'linhas' => 'required|array|min:1',
            'linhas.*.id' => 'nullable|exists:linhas_encomendas,id',
            'linhas.*.artigo_id' => 'required|exists:artigos,id',
            'linhas.*.fornecedor_id' => 'nullable|exists:entidades,id',
            'linhas.*.quantidade' => 'required|numeric|min:1',
            'linhas.*.preco_unitario' => 'required|numeric|min:0',
        ]);

        if ($validated['estado'] === 'Fechado' && empty($validated['data_da_proposta'])) {
            $validated['data_da_proposta'] = now();
        }

        $encomenda->update($validated);

        $idsMantidos = [];
        $total = 0;

        foreach ($validated['linhas'] as $linha) {
            $subtotal = $linha['quantidade'] * $linha['preco_unitario'];
            $total += $subtotal;

            if (!empty($linha['id'])) {
                $encomenda->linhas()->where('id', $linha['id'])->update([
                    'artigo_id' => $linha['artigo_id'],
                    'fornecedor_id' => $linha['fornecedor_id'],
                    'quantidade' => $linha['quantidade'],
                    'preco_unitario' => $linha['preco_unitario'],
                ]);
                $idsMantidos[] = $linha['id'];
            } else {
                $nova = $encomenda->linhas()->create($linha);
                $idsMantidos[] = $nova->id;
            }
        }

        $encomenda->linhas()->whereNotIn('id', $idsMantidos)->delete();

        $encomenda->update(['valor_total' => $total]);

        activity()
            ->useLog('Encomendas')
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->withProperties([
                'numero' => $encomenda->numero,
                'tipo' => $encomenda->tipo,
                'valor_total' => $encomenda->valor_total,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Atualizou a encomenda Nº ' . $encomenda->numero . ' de ' . optional($encomenda->cliente)->nome . ' (' . ucfirst($encomenda->tipo) . ').');

        return $this->redirecionarParaIndexPorTipo($encomenda)
            ->with('success', 'Encomenda atualizada com sucesso.');
    }

    public function destroy(Encomenda $encomenda)
    {
        $tipo = optional($encomenda->cliente)->tipo;

        $encomenda->delete();

        activity()
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->log('Eliminou a encomenda.');

        return $this->redirecionarParaIndexPorTipo(null, $tipo)->with('success', 'Encomenda eliminada com sucesso.');
    }

    public function show(Encomenda $encomenda)
    {
        $encomenda->load('cliente', 'linhas.artigo.iva');

        activity()
            ->useLog('Encomendas')
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->withProperties([
                'numero' => $encomenda->numero,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Visualizou os detalhes da encomenda nº {$encomenda->numero}.");

        return Inertia::render('Encomendas/Show', [
            'encomenda' => $encomenda,
        ]);
    }

    public function download(Encomenda $encomenda)
    {
        $encomenda->load(['cliente', 'linhas.artigo.iva']);
        $empresa = Empresa::first();

        activity()
            ->useLog('Encomendas')
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->withProperties([
                'numero' => $encomenda->numero,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Descarregou o PDF da encomenda nº {$encomenda->numero}.");

        $pdf = Pdf::loadView('pdfs.encomenda', compact('encomenda', 'empresa'));

        return $pdf->download("encomenda_{$encomenda->numero}.pdf");
    }

    public function converter(Encomenda $encomenda)
    {
        if ($encomenda->tipo !== 'cliente') {
            return redirect()->back()->with('error', 'Apenas encomendas de cliente podem ser convertidas.');
        }

        if ($encomenda->estado !== 'Fechado') {
            return redirect()->back()->with('error', 'A encomenda tem de estar no estado "Fechado" para ser convertida.');
        }

        $encomenda->load('linhas.artigo.iva', 'linhas.fornecedor');

        $linhasComFornecedor = $encomenda->linhas->filter(fn($l) => $l->fornecedor_id !== null);

        if ($linhasComFornecedor->isEmpty()) {
            return redirect()->back()->with('error', 'A encomenda não tem fornecedores associados aos artigos.');
        }

        $agrupadasPorFornecedor = $linhasComFornecedor->groupBy('fornecedor_id');
        $numeroAtual = (Encomenda::max('numero') ?? 2000);

        do {
            $numeroAtual++;
        } while (Encomenda::where('numero', $numeroAtual)->exists());

        foreach ($agrupadasPorFornecedor as $fornecedorId => $linhas) {
            $total = 0;

            foreach ($linhas as $linha) {
                $preco = $linha->quantidade * $linha->preco_unitario;
                $iva = $linha->artigo->iva->percentagem ?? 0;
                $total += $preco * (1 + $iva / 100);
            }

            $nova = Encomenda::create([
                'tipo' => 'fornecedor',
                'numero' => $numeroAtual++,
                'data_da_proposta' => now(),
                'validade' => now()->addDays(30),
                'cliente_id' => $fornecedorId,
                'estado' => 'Rascunho',
                'valor_total' => round($total, 2),
            ]);

            foreach ($linhas as $linha) {
                $nova->linhas()->create([
                    'artigo_id' => $linha->artigo_id,
                    'quantidade' => $linha->quantidade,
                    'preco_unitario' => $linha->preco_unitario,
                    'fornecedor_id' => $fornecedorId,
                ]);
            }
        }
        $tipo = optional($encomenda->cliente)->tipo;
        $numero = $encomenda->numero;

        $encomenda->delete();

        activity()
            ->useLog('Encomendas')
            ->performedOn($encomenda)
            ->causedBy(auth()->user())
            ->withProperties([
                'numero' => $numero,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Convertida e eliminada encomenda cliente nº {$numero} para encomendas por fornecedor.");

        return redirect()->route('encomendas.fornecedores')->with('success', 'Conversão realizada com sucesso.');
    }

    /**
     * Redireciona para a rota correta consoante o tipo de entidade relacionada com a encomenda.
     */
    private function redirecionarParaIndexPorTipo(?Encomenda $encomenda, ?string $tipoForcado = null)
    {
        $tipo = $tipoForcado ?? optional($encomenda->cliente)->tipo;

        return redirect()->route(
            $tipo === 'cliente' ? 'encomendas.clientes' :
            ($tipo === 'fornecedor' ? 'encomendas.fornecedores' : 'encomendas.index')
        );
    }

    private function gerarNumeroUnico(): int
    {
        $numero = (Encomenda::max('numero') ?? 2000) + 1;
        while (Encomenda::where('numero', $numero)->exists()) {
            $numero++;
        }
        return $numero;
    }
}
