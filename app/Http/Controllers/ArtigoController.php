<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use App\Models\Iva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArtigoController extends Controller
{
    public function index(Request $request)
    {
        $query = Artigo::with('iva');

        // Filtros
        if ($termo = $request->input('termo')) {
            $query->where(function ($q) use ($termo) {
                $q->where('referencia', 'like', "%$termo%")
                ->orWhere('nome', 'like', "%$termo%")
                ->orWhere('descricao', 'like', "%$termo%");
            });
        }

        if ($preco = $request->input('preco')) {
            $query->where('preco', $preco);
        }

        if ($iva = $request->input('iva_id')) {
            $query->where('iva_id', $iva);
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        // Ordenação
        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        $artigos = $query->paginate(10)->withQueryString();

        activity()
            ->useLog('Artigos')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à listagem de artigos.');

        return Inertia::render('Artigos/Index', [
            'artigos' => $artigos,
            'filtros' => $request->only(['termo', 'preco', 'iva_id', 'estado', 'sort', 'direction']),
            'ivas' => Iva::select('id', 'percentagem')->orderBy('percentagem')->get(),
        ]);
    }

    public function show(Artigo $artigo)
    {
        activity()
            ->useLog('Artigos')
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Visualizou o artigo: {$artigo->referencia}");

        return Inertia::render('Artigos/Show', [
            'artigo' => $artigo->load('iva'),
        ]);
    }

    public function create()
    {
        activity()
            ->useLog('Artigos')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de artigo.');

        return Inertia::render('Artigos/Create', [
            'ivas' => Iva::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'referencia' => 'required|unique:artigos,referencia',
            'nome' => 'required|string',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric',
            'iva_id' => 'required|exists:ivas,id',
            'estado' => 'required|in:Ativo,Inativo',
            'observacoes' => 'nullable|string',
            'foto' => 'nullable|file|image|max:2048',
        ]);

        // Processar foto se existir
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('artigos', 'local');
        }

        // Criar artigo
        $artigo = Artigo::create([
            'referencia' => $validated['referencia'],
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'] ?? null,
            'preco' => $validated['preco'],
            'iva_id' => $validated['iva_id'],
            'estado' => $validated['estado'],
            'observacoes' => $validated['observacoes'] ?? null,
            'foto' => $validated['foto'] ?? null,
        ]);

        activity()
            ->useLog('Artigos')
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Criou o artigo: ' . $artigo->referencia);

        return redirect()->route('artigos.index')->with('success', 'Artigo criado com sucesso.');
    }

    public function edit(Artigo $artigo)
    {
        activity()
            ->useLog('Artigos')
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Acedeu à edição do artigo: ' . $artigo->referencia);

        return Inertia::render('Artigos/Edit', [
            'artigo' => $artigo,
            'ivas' => Iva::all(),
        ]);
    }

    public function update(Request $request, Artigo $artigo)
    {
        $validated = $request->validate([
            'referencia' => 'required|unique:artigos,referencia,' . $artigo->id,
            'nome' => 'required',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric',
            'iva_id' => 'required|exists:ivas,id',
            'estado' => 'required|in:Ativo,Inativo',
            'observacoes' => 'nullable|string',
            'foto' => 'nullable|file|image|max:2048',
        ]);

        // Se foi enviada nova foto, guarda e substitui
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('artigos', 'local');
        }

        // Atualizar artigo
        $artigo->update([
            'referencia' => $validated['referencia'],
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'] ?? null,
            'preco' => $validated['preco'],
            'iva_id' => $validated['iva_id'],
            'estado' => $validated['estado'],
            'observacoes' => $validated['observacoes'] ?? null,
            'foto' => $validated['foto'] ?? $artigo->foto,
        ]);

        activity()
            ->useLog('Artigos')
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Atualizou o artigo: ' . $artigo->referencia);

        return redirect()->route('artigos.index')->with('success', 'Artigo atualizado com sucesso.');
    }

    public function destroy(Artigo $artigo)
    {
        $artigo->delete();

        activity()
            ->useLog('Artigos')
            ->performedOn($artigo)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Eliminou o artigo: ' . $artigo->referencia);

        return redirect()->route('artigos.index')->with('success', 'Artigo eliminado com sucesso.');
    }
}
