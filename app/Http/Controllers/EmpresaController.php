<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresa = Empresa::first();
        return view('empresa.index', compact('empresa'));
    }

    public function edit()
    {
        $empresa = Empresa::first();
        return view('empresa.edit', compact('empresa'));
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
            'logotipo' => 'nullable|image'
        ]);

        if ($request->hasFile('logotipo')) {
            $data['logotipo'] = $request->file('logotipo')->store('logos', 'private');
        }

        $empresa->update($data);

        activity()
            ->performedOn($empresa)
            ->causedBy(auth()->user())
            ->log('Atualizou os dados da empresa.');

        return redirect()->route('empresa.index')->with('success', 'Dados da empresa atualizados com sucesso.');
    }
}
