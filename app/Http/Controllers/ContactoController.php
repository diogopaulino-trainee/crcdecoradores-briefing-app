<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Funcao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ContactoController extends Controller
{
    public function index(Request $request)
    {
        $query = Contacto::query()->with(['entidade', 'funcao']);

        // Filtros dinâmicos
        if ($nome = $request->input('nome')) {
            $query->where(function ($q) use ($nome) {
                $q->where('primeiro_nome', 'like', "%$nome%")
                ->orWhere('apelido', 'like', "%$nome%");
            });
        }

        if ($entidadeId = $request->input('entidade_id')) {
            $query->where('entidade_id', $entidadeId);
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        if ($rgpd = $request->input('consentimento_rgpd')) {
            $query->where('consentimento_rgpd', $rgpd);
        }

        // Ordenação
        $sort = $request->input('sort', 'primeiro_nome');
        $direction = $request->input('direction', 'asc');

        if ($sort === 'entidade_nome') {
            $query->join('entidades', 'contactos.entidade_id', '=', 'entidades.id')
                ->orderBy('entidades.nome', $direction)
                ->select('contactos.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        // Paginação
        $contactos = $query->paginate(10)->withQueryString();

        // Filtros para o frontend
        $filtros = $request->only(['nome', 'entidade_id', 'estado', 'consentimento_rgpd', 'sort', 'direction']);

        activity()
            ->useLog('Contactos')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à listagem de contactos.');

        return Inertia::render('Contactos/Index', [
            'contactos' => $contactos,
            'filtros' => $filtros,
            'entidades' => Entidade::select('id', 'nome')->orderBy('nome')->get(),
        ]);
    }

    public function show(Contacto $contacto)
    {
        $contacto->load(['entidade', 'funcao']);

        activity()
            ->useLog('Contactos')
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Visualizou o contacto: ' . $contacto->primeiro_nome . ' ' . $contacto->apelido);

        return Inertia::render('Contactos/Show', [
            'contacto' => $contacto,
        ]);
    }

    public function create()
    {
        $proximoNumero = (Contacto::max('numero') ?? 0) + 1;

        activity()
            ->useLog('Contactos')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de contacto.');

        return Inertia::render('Contactos/Create', [
            'entidades' => Entidade::all(),
            'funcoes' => Funcao::all(),
            'proximoNumero' => $proximoNumero,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entidade_id' => ['required', 'exists:entidades,id'],
            'primeiro_nome' => ['required', 'string', 'max:255'],
            'apelido' => ['required', 'string', 'max:255'],
            'funcao_id' => ['required', 'exists:funcoes,id'],
            'telefone' => ['required', 'string', 'max:50'],
            'telemovel' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:contactos,email'],
            'consentimento_rgpd' => ['required', 'in:Sim,Não'],
            'observacoes' => ['nullable', 'string'],
            'estado' => ['required', 'in:Ativo,Inativo'],
        ]);

        $ultimoNumero = Contacto::max('numero') ?? 0;
        $validated['numero'] = $ultimoNumero + 1;

        $contacto = Contacto::create($validated);

        activity()
            ->useLog('Contactos')
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->withProperties([
                'entidade' => $contacto->entidade_id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Criou o contacto: ' . $contacto->primeiro_nome . ' ' . $contacto->apelido);

        return redirect()->route('contactos.index')->with('success', 'Contacto criado com sucesso.');
    }

    public function edit(Contacto $contacto)
    {
        activity()
            ->useLog('Contactos')
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Acedeu à edição do contacto: ' . $contacto->primeiro_nome . ' ' . $contacto->apelido);

        return Inertia::render('Contactos/Edit', [
            'contacto' => $contacto,
            'entidades' => Entidade::all(),
            'funcoes' => Funcao::all(),
        ]);
    }

    public function update(Request $request, Contacto $contacto)
    {
        $validated = $request->validate([
            'entidade_id' => ['required', 'exists:entidades,id'],
            'primeiro_nome' => ['required', 'string', 'max:255'],
            'apelido' => ['required', 'string', 'max:255'],
            'funcao_id' => ['required', 'exists:funcoes,id'],
            'telefone' => ['required', 'string', 'max:50'],
            'telemovel' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('contactos', 'email')->ignore($contacto->id),
            ],
            'consentimento_rgpd' => ['required', 'in:Sim,Não'],
            'observacoes' => ['nullable', 'string'],
            'estado' => ['required', 'in:Ativo,Inativo'],
        ]);

        $contacto->update($validated);

        activity()
            ->useLog('Contactos')
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Atualizou o contacto: ' . $contacto->primeiro_nome . ' ' . $contacto->apelido);

        return redirect()->route('contactos.index')->with('success', 'Contacto atualizado com sucesso.');
    }

    public function destroy(Contacto $contacto)
    {
        $nome = $contacto->primeiro_nome . ' ' . $contacto->apelido;
        $contacto->delete();

        activity()
            ->useLog('Contactos')
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Eliminou o contacto: ' . $nome);

        return redirect()->route('contactos.index')->with('success', 'Contacto eliminado com sucesso.');
    }
}
