<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use App\Models\Encomenda;
use App\Models\Entidade;
use App\Models\Proposta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropostaController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'data_da_proposta');
        $direction = $request->input('direction', 'desc');

        $propostas = Proposta::with('cliente')
            ->when($request->input('termo'), function ($query, $termo) {
                $query->where('numero', 'like', "%$termo%")
                    ->orWhereHas('cliente', fn($q) => $q->where('nome', 'like', "%$termo%"));
            })
            ->when($request->input('estado'), fn($q, $estado) => $q->where('estado', $estado))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Propostas/Index', [
            'propostas' => $propostas,
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }

    public function show(Proposta $proposta)
    {
        $proposta->load(['cliente', 'linhas.artigo.iva']);

        return Inertia::render('Propostas/Show', [
            'proposta' => $proposta,
        ]);
    }

    public function create()
    {
        $clientes = Entidade::where('tipo', 'cliente')->get();
        $fornecedores = Entidade::where('tipo', 'fornecedor')->get();

        $ultimoNumero = Proposta::max('numero') ?? 0;
        $proximoNumero = $ultimoNumero + 1;

        return Inertia::render('Propostas/Create', [
            'clientes' => $clientes,
            'artigos' => Artigo::all(),
            'fornecedores' => $fornecedores,
            'proximoNumero' => $proximoNumero,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:propostas,numero',
            'data_da_proposta' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
            'validade' => 'required|date',
            'linhas' => 'required|array|min:1',
            'linhas.*.artigo_id' => 'required|exists:artigos,id',
            'linhas.*.fornecedor_id' => 'required|exists:entidades,id',
            'linhas.*.quantidade' => 'required|numeric|min:1',
            'linhas.*.preco_unitario' => 'required|numeric|min:0',
        ]);

        $proposta = Proposta::create($validated);

        if ($request->has('linhas')) {
            $total = 0;
            foreach ($request->linhas as $linha) {
                $proposta->linhas()->create([
                    'artigo_id' => $linha['artigo_id'],
                    'quantidade' => $linha['quantidade'],
                    'preco_unitario' => $linha['preco_unitario'],
                ]);

                $total += $linha['quantidade'] * $linha['preco_unitario'];
            }
            $proposta->update(['valor_total' => $total]);
        }

        activity()
            ->performedOn($proposta)
            ->causedBy(auth()->user())
            ->log('Criou uma proposta.');

        return redirect()->route('propostas.index')->with('success', 'Proposta criada com sucesso.');
    }

    public function edit(Proposta $proposta)
    {
        $clientes = Entidade::where('tipo', 'cliente')->get();
        $fornecedores = Entidade::where('tipo', 'fornecedor')->get();
        $artigos = Artigo::all();

        return Inertia::render('Propostas/Edit', [
            'proposta' => $proposta->load('linhas:id,proposta_id,artigo_id,quantidade,preco_unitario,fornecedor_id'),
            'clientes' => $clientes,
            'fornecedores' => $fornecedores,
            'artigos' => $artigos,
        ]);
    }

    public function update(Request $request, Proposta $proposta)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:propostas,numero,' . $proposta->id,
            'data_da_proposta' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
            'validade' => 'required|date',
        ]);

        $proposta->update($validated);

        if ($request->has('linhas')) {
            $idsRecebidos = [];
        
            foreach ($request->linhas as $linha) {
                $data = [
                    'artigo_id' => $linha['artigo_id'],
                    'quantidade' => $linha['quantidade'],
                    'preco_unitario' => $linha['preco_unitario'],
                    'fornecedor_id' => $linha['fornecedor_id'] ?? null,
                ];
        
                if (isset($linha['id'])) {
                    $linhaModel = $proposta->linhas()->find($linha['id']);
                    if ($linhaModel) {
                        $linhaModel->update($data);
                        $idsRecebidos[] = $linha['id'];
                    }
                } else {
                    $novaLinha = $proposta->linhas()->create($data);
                    $idsRecebidos[] = $novaLinha->id;
                }
            }
        
            $proposta->linhas()->whereNotIn('id', $idsRecebidos)->delete();
        
            $total = $proposta->linhas->sum(fn($l) => $l->quantidade * $l->preco_unitario);
            $proposta->update(['valor_total' => $total]);
        }

        activity()
            ->performedOn($proposta)
            ->causedBy(auth()->user())
            ->log('Atualizou a proposta.');

        return redirect()->route('propostas.index')->with('success', 'Proposta atualizada com sucesso.');
    }

    public function destroy(Proposta $proposta)
    {
        $proposta->delete();

        activity()
            ->performedOn($proposta)
            ->causedBy(auth()->user())
            ->log('Eliminou a proposta.');

        return redirect()->route('propostas.index')->with('success', 'Proposta eliminada com sucesso.');
    }

    public function download(Proposta $proposta)
    {
        $proposta->load(['cliente', 'linhas.artigo.iva']);

        $totalComIva = $proposta->linhas->reduce(function ($carry, $linha) {
            $preco = $linha->quantidade * $linha->preco_unitario;
            $iva = $linha->artigo->iva->percentagem ?? 0;
            return $carry + ($preco * (1 + ($iva / 100)));
        }, 0);

        $pdf = Pdf::loadView('pdfs.proposta', compact('proposta', 'totalComIva'));
        return $pdf->download("proposta_{$proposta->numero}.pdf");
    }

    public function converter(Proposta $proposta)
    {
        $proposta = Proposta::with('linhas.artigo.iva', 'linhas.fornecedor', 'cliente')->find($proposta->id);

        $total = 0;

        foreach ($proposta->linhas as $linha) {
            $precoBase = $linha->quantidade * $linha->preco_unitario;
            $percentagemIva = $linha->artigo->iva->percentagem ?? 0;
            $total += $precoBase * (1 + $percentagemIva / 100);
        }

        $encomenda = Encomenda::create([
            'tipo' => 'cliente',
            'numero' => (Encomenda::max('numero') ?? 2000) + 1,
            'data_da_proposta' => now(),
            'validade' => now()->addDays(30),
            'cliente_id' => $proposta->cliente_id,
            'estado' => 'Rascunho',
            'valor_total' => round($total, 2),
        ]);

        foreach ($proposta->linhas as $linha) {
            $encomenda->linhas()->create([
                'artigo_id' => $linha->artigo_id,
                'quantidade' => $linha->quantidade,
                'preco_unitario' => $linha->preco_unitario,
                'fornecedor_id' => $linha->fornecedor_id,
            ]);
        }

        $proposta->delete();

        activity()
            ->performedOn($proposta)
            ->causedBy(auth()->user())
            ->log('Proposta convertida em encomenda.');

        return redirect()->route('encomendas.clientes')->with('success', 'Proposta convertida em encomenda.');
    }
}
