<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class FicheiroSeguroController extends Controller
{
    public function ver($caminho)
    {
        // Garante que só utilizadores autenticados acedem
        if (!auth()->check()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Verifica se o ficheiro existe no disco local (privado)
        if (!Storage::disk('local')->exists($caminho)) {
            abort(404, 'Ficheiro não encontrado.');
        }

        activity()
            ->useLog('Ficheiros Seguros')
            ->causedBy(auth()->user())
            ->withProperties([
                'ficheiro' => $caminho,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Acedeu ao ficheiro seguro: ' . $caminho);

        // Serve o ficheiro diretamente
        return response()->file(storage_path('app/private/' . $caminho));
    }
}
