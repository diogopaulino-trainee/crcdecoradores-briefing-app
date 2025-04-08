<?php

namespace App\Http\Controllers;

use App\Mail\ComprovativoPagamentoFornecedor;
use App\Models\Encomenda;
use App\Models\Entidade;
use App\Models\FaturaFornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class FaturaFornecedorController extends Controller
{
    public function index(Request $request)
    {
        $query = FaturaFornecedor::with(['fornecedor', 'encomendaFornecedor']);

        if ($request->filled('termo')) {
            $query->where('numero', 'like', '%' . $request->termo . '%')
                ->orWhereHas('fornecedor', fn($q) => $q->where('nome', 'like', '%' . $request->termo . '%'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $sort = $request->input('sort', 'data_da_fatura');
        $direction = $request->input('direction', 'desc');

        $query->orderBy($sort, $direction);

        $faturas = $query->paginate(10)->withQueryString();

        return Inertia::render('FaturasFornecedores/Index', [
            'faturas' => $faturas,
            'filtros' => $request->only(['termo', 'estado', 'sort', 'direction']),
        ]);
    }
    
    public function create()
    {
        $fornecedores = Entidade::where('tipo', 'fornecedor')->get();

        $encomendasFornecedor = Encomenda::where('tipo', 'fornecedor')->get()->map(function ($encomenda) {
            return [
                'id' => $encomenda->id,
                'numero' => $encomenda->numero,
                'data_da_proposta' => $encomenda->data_da_proposta,
                'fornecedor_id' => $encomenda->cliente_id,
                'valor_total' => $encomenda->valor_total,
            ];
        });

        $proximoNumero = FaturaFornecedor::max('numero') + 1 ?? 1;

        return Inertia::render('FaturasFornecedores/Create', [
            'fornecedores' => $fornecedores,
            'encomendasFornecedor' => $encomendasFornecedor,
            'proximoNumero' => $proximoNumero,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:faturas_fornecedores,numero',
            'data_da_fatura' => 'required|date',
            'data_de_vencimento' => 'required|date',
            'fornecedor_id' => 'required|exists:entidades,id',
            'encomenda_fornecedor_id' => 'required|exists:encomendas,id',
            'valor_total' => 'required|numeric',
            'estado' => 'required|in:Pendente,Paga',
            'documento.*' => 'file|mimes:pdf,jpg,jpeg,png',
            'documento' => 'nullable|array',
            'comprovativo_pagamento.*' => 'file|mimes:pdf,jpg,jpeg,png',
            'comprovativo_pagamento' => 'nullable|array',
        ]);

        if ($request->hasFile('documento')) {
            $documentos = [];
            foreach ($request->file('documento') as $ficheiro) {
                $documentos[] = $ficheiro->store('faturas/documentos', 'private');
            }
            $validated['documento'] = $documentos;
        }

        if ($request->hasFile('comprovativo_pagamento')) {
            $comprovativos = [];
            foreach ($request->file('comprovativo_pagamento') as $ficheiro) {
                $comprovativos[] = $ficheiro->store('faturas/comprovativos', 'private');
            }
            $validated['comprovativo_pagamento'] = $comprovativos;
        }

        $fatura = FaturaFornecedor::create($validated);

        // Enviar email se a fatura for criada já com estado "Paga"
        if ($validated['estado'] === 'Paga' && isset($validated['comprovativo_pagamento']) && $fatura->fornecedor?->email) {
            Mail::to($fatura->fornecedor->email)->send(new ComprovativoPagamentoFornecedor($fatura));
        }

        activity()->performedOn($fatura)->causedBy(auth()->user())->log('Criou uma fatura de fornecedor.');

        return redirect()->route('faturas.index')->with('success', 'Fatura do fornecedor criada com sucesso.');
    }

    public function edit(FaturaFornecedor $faturaFornecedor)
    {
        $faturaFornecedor->load(['fornecedor', 'encomendaFornecedor']);

        $fornecedores = Entidade::where('tipo', 'fornecedor')->get();

        $encomendasFornecedor = Encomenda::where('tipo', 'fornecedor')->get()->map(function ($encomenda) {
            return [
                'id' => $encomenda->id,
                'numero' => $encomenda->numero,
                'data_da_proposta' => $encomenda->data_da_proposta,
                'fornecedor_id' => $encomenda->cliente_id,
                'valor_total' => $encomenda->valor_total,
            ];
        });

        return Inertia::render('FaturasFornecedores/Edit', [
            'fatura' => [
                ...$faturaFornecedor->toArray(),
                'documento' => $faturaFornecedor->documento ?? [],
                'comprovativo_pagamento' => $faturaFornecedor->comprovativo_pagamento ?? [],
            ],
            'fornecedores' => $fornecedores,
            'encomendasFornecedor' => $encomendasFornecedor,
        ]);
    }

    public function update(Request $request, FaturaFornecedor $faturaFornecedor)
    {
        if (empty($request->all()) && $request->isMethod('POST')) {
            Log::warning('[Fatura Update] Request vazia (possível limite de upload ultrapassado).');
            throw new SuspiciousOperationException('O tamanho total do envio ultrapassa o limite permitido.');
        }

        $validated = $request->validate([
            'numero' => 'required|unique:faturas_fornecedores,numero,' . $faturaFornecedor->id,
            'data_da_fatura' => 'required|date',
            'data_de_vencimento' => 'required|date',
            'fornecedor_id' => 'required|exists:entidades,id',
            'encomenda_fornecedor_id' => 'nullable|exists:encomendas,id',
            'valor_total' => 'required|numeric',
            'estado' => 'required|in:Pendente,Paga',
            'documento.*' => 'file|mimes:pdf,jpg,jpeg,png',
            'documento' => 'nullable|array',
            'comprovativo_pagamento.*' => 'file|mimes:pdf,jpg,jpeg,png',
            'comprovativo_pagamento' => 'nullable|array',
            'remover_documentos' => 'nullable|array',
            'remover_comprovativos' => 'nullable|array',
        ]);

        $documentos = (array) $faturaFornecedor->documento;
        $comprovativos = (array) $faturaFornecedor->comprovativo_pagamento;

        if ($request->filled('remover_documentos')) {
            Log::info('[Fatura Update] Removendo documentos', $request->remover_documentos);
            foreach ($request->remover_documentos as $ficheiro) {
                Storage::disk('private')->delete($ficheiro);
                $documentos = array_diff($documentos, [$ficheiro]);
            }
        }

        if ($request->filled('remover_comprovativos')) {
            Log::info('[Fatura Update] Removendo comprovativos', $request->remover_comprovativos);
            foreach ($request->remover_comprovativos as $ficheiro) {
                Storage::disk('private')->delete($ficheiro);
                $comprovativos = array_diff($comprovativos, [$ficheiro]);
            }
        }

        if ($request->hasFile('documento')) {
            Log::info('[Fatura Update] Novos documentos recebidos');
            foreach ($request->file('documento') as $ficheiro) {
                $path = $ficheiro->store('faturas/documentos', 'private');
                Log::info('[Fatura Update] Documento guardado', ['path' => $path]);
                $documentos[] = $path;
            }
        }

        if ($request->hasFile('comprovativo_pagamento')) {
            Log::info('[Fatura Update] Novos comprovativos recebidos');
            foreach ($request->file('comprovativo_pagamento') as $ficheiro) {
                $path = $ficheiro->store('faturas/comprovativos', 'private');
                Log::info('[Fatura Update] Comprovativo guardado', ['path' => $path]);
                $comprovativos[] = $path;
            }
        }

        $validated['documento'] = array_values($documentos);
        $validated['comprovativo_pagamento'] = array_values($comprovativos);

        Log::info('[Fatura Update] Dados validados prontos para update', $validated);

        $faturaFornecedor->update($validated);

        if (
            $validated['estado'] === 'Paga' &&
            !empty($validated['comprovativo_pagamento']) &&
            $faturaFornecedor->fornecedor?->email
        ) {
            Log::info('[Fatura Update] A enviar email para fornecedor', ['email' => $faturaFornecedor->fornecedor->email]);

            Mail::to($faturaFornecedor->fornecedor->email)->send(new ComprovativoPagamentoFornecedor($faturaFornecedor));
        }

        activity()->performedOn($faturaFornecedor)->causedBy(auth()->user())->log('Atualizou a fatura do fornecedor.');

        Log::info('[Fatura Update] Update finalizado com sucesso');

        return redirect()->route('faturas.index')->with('success', 'Fatura atualizada com sucesso.');
    }

    public function show($id)
    {
        $faturaFornecedor = FaturaFornecedor::with([
            'fornecedor',
            'encomendaFornecedor.fornecedorEncomenda',
        ])->findOrFail($id);

        return Inertia::render('FaturasFornecedores/Show', [
            'fatura' => $faturaFornecedor,
        ]);
    }

    public function destroy(FaturaFornecedor $faturaFornecedor)
    {
        $faturaFornecedor->delete();

        activity()->performedOn($faturaFornecedor)->causedBy(auth()->user())->log('Eliminou a fatura do fornecedor.');

        return redirect()->route('faturas.index')->with('success', 'Fatura eliminada com sucesso.');
    }
}
