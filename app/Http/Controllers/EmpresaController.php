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

        activity()
            ->useLog('Empresa')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Acedeu à página de dados da empresa.');

        return Inertia::render('Empresa/Index', [
            'empresa' => $empresa,
        ]);
    }

    public function update(Request $request)
    {
        $empresa = Empresa::first();

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:255',
            'numero_contribuinte' => 'nullable|string|max:20',
            'logotipo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logotipo')) {
            if ($empresa->logotipo && Storage::disk('private')->exists($empresa->logotipo)) {
                Storage::disk('private')->delete($empresa->logotipo);
            }

            $data['logotipo'] = $request->file('logotipo')->store('logos', 'private');
        } else {
            unset($data['logotipo']);
        }

        $empresa->update($data);

        activity()
            ->useLog('Empresa')
            ->performedOn($empresa)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Atualizou os dados da empresa.');

        return redirect()->route('empresa.index')->with('success', 'Dados da empresa atualizados com sucesso.');
    }
}
