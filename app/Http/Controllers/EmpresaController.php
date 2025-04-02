<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresa = Empresa::first();

        return Inertia::render('Empresa/Index', [
            'empresa' => $empresa,
        ]);
    }

    public function edit()
    {
        $empresa = Empresa::first();

        return Inertia::render('Empresa/Edit', [
            'empresa' => $empresa,
        ]);
    }

    public function update(Request $request)
    {
        $empresa = Empresa::first();

        $data = $request->validate([
            'nome' => 'required',
            'morada' => 'nullable',
            'codigo_postal' => 'nullable',
            'localidade' => 'nullable',
            'numero_contribuinte' => 'nullable',
            'logotipo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logotipo')) {
            $data['logotipo'] = $request->file('logotipo')->store('logos', 'private');
        }

        $empresa->update($data);

        activity()
            ->performedOn($empresa)
            ->causedBy(auth()->user())
            ->log('Atualizou os dados da empresa.');

        return redirect()->route('empresas.index')->with('success', 'Dados da empresa atualizados com sucesso.');
    }
}
