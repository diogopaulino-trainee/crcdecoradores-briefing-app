<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\FaturaFornecedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaturaFornecedorController extends Controller
{
    public function index()
    {
        $faturas = FaturaFornecedor::with(['fornecedor', 'encomendaFornecedor'])->get();

        return Inertia::render('FaturasFornecedores/Index', [
            'faturas' => $faturas,
        ]);
    }

    public function create()
    {
        $fornecedores = Entidade::where('tipo', 'fornecedor')->get();

        return Inertia::render('FaturasFornecedores/Create', [
            'fornecedores' => $fornecedores,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|unique:faturas_fornecedores,numero',
            'data_da_fatura' => 'required|date',
            'data_de_vencimento' => 'required|date',
            'fornecedor_id' => 'required|exists:entidades,id',
            'valor_total' => 'required|numeric',
        ]);

        $fatura = FaturaFornecedor::create($request->all());

        activity()
            ->performedOn($fatura)
            ->causedBy(auth()->user())
            ->log('Criou uma fatura de fornecedor.');

        return redirect()->route('faturas-fornecedores.index')->with('success', 'Fatura do fornecedor criada com sucesso.');
    }

    public function edit(FaturaFornecedor $faturaFornecedor)
    {
        $fornecedores = Entidade::where('tipo', 'fornecedor')->get();

        return Inertia::render('FaturasFornecedores/Edit', [
            'fatura' => $faturaFornecedor,
            'fornecedores' => $fornecedores,
        ]);
    }

    public function update(Request $request, FaturaFornecedor $faturaFornecedor)
    {
        $request->validate([
            'numero' => 'required|unique:faturas_fornecedores,numero,' . $faturaFornecedor->id,
            'data_da_fatura' => 'required|date',
            'data_de_vencimento' => 'required|date',
            'fornecedor_id' => 'required|exists:entidades,id',
            'valor_total' => 'required|numeric',
        ]);

        $faturaFornecedor->update($request->all());

        activity()
            ->performedOn($faturaFornecedor)
            ->causedBy(auth()->user())
            ->log('Atualizou a fatura do fornecedor.');

        return redirect()->route('faturas-fornecedores.index')->with('success', 'Fatura atualizada com sucesso.');
    }

    public function destroy(FaturaFornecedor $faturaFornecedor)
    {
        $faturaFornecedor->delete();

        activity()
            ->performedOn($faturaFornecedor)
            ->causedBy(auth()->user())
            ->log('Eliminou a fatura do fornecedor.');

        return redirect()->route('faturas-fornecedores.index')->with('success', 'Fatura eliminada com sucesso.');
    }
}
