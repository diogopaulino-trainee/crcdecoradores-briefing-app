<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ContactoController extends Controller
{
    public function index(Request $request)
    {
        $query = Contacto::query()->with('entidade');

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

        return Inertia::render('Contactos/Index', [
            'contactos' => $contactos,
            'filtros' => $filtros,
            'entidades' => Entidade::select('id', 'nome')->orderBy('nome')->get(),
        ]);
    }

    public function show(Contacto $contacto)
    {
        $contacto->load('entidade');

        return Inertia::render('Contactos/Show', [
            'contacto' => $contacto,
        ]);
    }

    public function create()
    {
        return Inertia::render('Contactos/Create', [
            'entidades' => Entidade::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entidade_id' => ['required', 'exists:entidades,id'],
            'primeiro_nome' => ['required', 'string', 'max:255'],
            'apelido' => ['required', 'string', 'max:255'],
            'funcao' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:50'],
            'telemovel' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:contactos,email'],
            'consentimento_rgpd' => ['required', 'in:sim,nao'],
            'observacoes' => ['nullable', 'string'],
            'estado' => ['required', 'in:ativo,inativo'],
        ]);

        $ultimoNumero = Contacto::max('numero') ?? 0;
        $validated['numero'] = $ultimoNumero + 1;

        $contacto = Contacto::create($validated);

        activity()
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->withProperties(['entidade' => $contacto->entidade_id])
            ->log('Criou um contacto.');

        return redirect()->route('contactos.index')->with('success', 'Contacto criado com sucesso.');
    }

    public function edit(Contacto $contacto)
    {
        return Inertia::render('Contactos/Edit', [
            'contacto' => $contacto,
            'entidades' => Entidade::all(),
        ]);
    }

    public function update(Request $request, Contacto $contacto)
    {
        $validated = $request->validate([
            'entidade_id' => ['required', 'exists:entidades,id'],
            'primeiro_nome' => ['required', 'string', 'max:255'],
            'apelido' => ['required', 'string', 'max:255'],
            'funcao' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:50'],
            'telemovel' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('contactos', 'email')->ignore($contacto->id),
            ],
            'consentimento_rgpd' => ['required', 'in:sim,nao'],
            'observacoes' => ['nullable', 'string'],
            'estado' => ['required', 'in:ativo,inativo'],
        ]);

        $contacto->update($validated);

        activity()
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->withProperties(['entidade' => $contacto->entidade_id])
            ->log('Atualizou o contacto.');

        return redirect()->route('contactos.index')->with('success', 'Contacto atualizado com sucesso.');
    }

    public function destroy(Contacto $contacto)
    {
        $contacto->delete();

        activity()
            ->performedOn($contacto)
            ->causedBy(auth()->user())
            ->log('Eliminou o contacto.');

        return redirect()->route('contactos.index')->with('success', 'Contacto eliminado com sucesso.');
    }
}
