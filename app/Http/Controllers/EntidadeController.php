<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Services\VatValidationService;

class EntidadeController extends Controller
{
    public function index(Request $request)
    {
        return $this->listarEntidadesPorTipo($request, null);
    }

    public function clientes(Request $request)
    {
        return $this->listarEntidadesPorTipo($request, 'cliente');
    }

    public function fornecedores(Request $request)
    {
        return $this->listarEntidadesPorTipo($request, 'fornecedor');
    }

    private function listarEntidadesPorTipo(Request $request, $tipo = null)
    {
        $query = Entidade::with('pais');

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        // Filtros individuais
        if ($nome = $request->input('nome')) {
            $query->where('nome', 'like', "%$nome%");
        }

        if ($nif = $request->input('nif')) {
            $query->where('nif_hash', hash('sha256', $nif));
        }

        if ($email = $request->input('email')) {
            $query->where('email', 'like', "%$email%");
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        if ($rgpd = $request->input('consentimento_rgpd')) {
            $query->where('consentimento_rgpd', $rgpd);
        }

        if ($paisId = $request->input('pais_id')) {
            $query->where('pais_id', $paisId);
        }

        // Ordenação
        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        $entidades = $query->paginate(10)->withQueryString();

        $filtros = $request->only(['nome', 'nif', 'email', 'estado', 'consentimento_rgpd', 'pais_id', 'sort', 'direction']);

        activity()
            ->useLog('Entidades')
            ->causedBy(auth()->user())
            ->withProperties([
                'tipo' => $tipo ?? 'todos',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Listou entidades do tipo ' . ($tipo ?? 'todos') . '.');

        return Inertia::render('Entidades/Index', [
            'entidades' => $entidades,
            'filtro' => $tipo ?? 'todos',
            'filtros' => $filtros,
            'paises' => \App\Models\Pais::select('id', 'nome')->orderBy('nome')->get(),
        ]);
    }

    public function show(Entidade $entidade)
    {
        $entidade->load('pais');

        activity()
            ->useLog('Entidades')
            ->performedOn($entidade)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Visualizou os detalhes da entidade.');

        return Inertia::render('Entidades/Show', [
            'entidade' => $entidade,
        ]);
    }

    public function create(Request $request)
    {
        $paises = Pais::all();
        $ultimoNumero = Entidade::max('numero') ?? 0;
        $proximoNumero = $ultimoNumero + 1;

        activity()
            ->useLog('Entidades')
            ->causedBy(auth()->user())
            ->withProperties([
                'tipo' => $request->input('tipo', 'cliente'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu ao formulário de criação de entidade.');

        return Inertia::render('Entidades/Create', [
            'tipo' => $request->input('tipo', 'cliente'),
            'paises' => $paises,
            'proximoNumero' => $proximoNumero,
        ]);
    }

    public function store(Request $request, VatValidationService $vatService)
    {
        $request->validate([
            'nif' => ['required'],
            'nome' => ['required', 'string', 'max:255'],
            'morada' => ['required', 'string', 'max:255'],
            'codigo_postal' => ['required', 'regex:/^\d{4}-\d{3}$/'],
            'localidade' => ['required', 'string', 'max:255'],
            'pais_id' => ['required', 'exists:paises,id'],
            'telefone' => ['required', 'string', 'max:20'],
            'telemovel' => ['required', 'string', 'max:20'],
            'website' => ['required', 'url'],
            'email' => ['required', 'email', 'unique:entidades,email'],
            'consentimento_rgpd' => ['required', 'in:Sim,Não'],
            'observacoes' => ['nullable', 'string'],
            'estado' => ['required', 'in:Ativo,Inativo'],
        ]);
    
        // Verifica se o NIF já existe
        $existe = Entidade::where('nif_hash', hash('sha256', $request->nif))->exists();
        if ($existe) {
            throw ValidationException::withMessages([
                'nif' => ['O NIF já existe.'],
            ]);
        }
    
        $pais = Pais::find($request->pais_id);
        $nifValidoVies = true;
    
        // Verifica se a validação VIES está ativada na configuração
        if (config('vies.enabled') && $pais) {
            try {
                // Realiza a validação do NIF via VIES para o país selecionado
                $nifValidoVies = $vatService->validarNifComVies($request->nif, $pais->codigo);
            } catch (\Throwable $e) {
                // Caso ocorra erro na validação, mostra um aviso
                session()->flash('warning', 'Não foi possível validar o NIF com o VIES neste momento.');
            }
    
            // Se a validação falhar, exibe um aviso
            if (!$nifValidoVies) {
                throw ValidationException::withMessages([
                    'nif' => ['O NIF não é válido segundo o VIES.'],
                ]);
            }
        }

        // Obtém o próximo número de entidade
        $ultimoNumero = Entidade::max('numero') ?? 0;
        $proximoNumero = $ultimoNumero + 1;

        // Atualiza a requisição com o novo número e hash do NIF
        $request->merge([
            'numero' => $proximoNumero,
            'nif_hash' => hash('sha256', $request->nif),
        ]);

        // Cria a nova entidade
        $entidade = Entidade::create($request->only([
            'tipo', 'numero', 'nif', 'nome', 'morada', 'codigo_postal', 'localidade',
            'pais_id', 'telefone', 'telemovel', 'website', 'email',
            'consentimento_rgpd', 'observacoes', 'estado'
        ]));

        activity()
            ->useLog('Entidades')
            ->performedOn($entidade)
            ->causedBy(auth()->user())
            ->withProperties([
                'tipo' => $entidade->tipo,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Criou uma entidade' . (!$nifValidoVies ? ' (sem validação VIES)' : ' com validação VIES.'));

        // Redireciona para a rota apropriada
        return redirect()->route(
            $entidade->tipo === 'cliente' ? 'clientes.index' :
            ($entidade->tipo === 'fornecedor' ? 'fornecedores.index' : 'entidades.index')
        )->with('success', 'Entidade criada com sucesso.');
    }

    public function edit(Entidade $entidade)
    {
        $paises = Pais::select('id', 'nome')->orderBy('nome')->get();

        activity()
        ->useLog('Entidades')
        ->causedBy(auth()->user())
        ->performedOn($entidade)
        ->withProperties([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ])
        ->log('Acedeu à edição da entidade: ' . $entidade->nome . ' (Nº ' . $entidade->numero . ')');

        return Inertia::render('Entidades/Edit', [
            'entidade' => $entidade,
            'paises' => $paises,
        ]);
    }

    public function update(Request $request, Entidade $entidade)
    {
        $request->validate([
            'nif' => ['required'],
            'nome' => ['required', 'string', 'max:255'],
            'morada' => ['required', 'string', 'max:255'],
            'codigo_postal' => ['required', 'regex:/^\d{4}-\d{3}$/'],
            'localidade' => ['required', 'string', 'max:255'],
            'pais_id' => ['required', 'exists:paises,id'],
            'telefone' => ['required', 'string', 'max:20'],
            'telemovel' => ['required', 'string', 'max:20'],
            'website' => ['required', 'url'],
            'email' => ['required', 'email', 'unique:entidades,email,' . $entidade->id],
            'consentimento_rgpd' => ['required', 'in:Sim,Não'],
            'observacoes' => ['nullable', 'string'],
            'estado' => ['required', 'in:Ativo,Inativo'],
        ]);

        $nifHash = hash('sha256', $request->nif);

        // Verifique apenas se o NIF foi alterado
        if ($entidade->nif_hash !== $nifHash) {
            $existe = Entidade::where('nif_hash', $nifHash)
                ->where('id', '!=', $entidade->id)
                ->exists();

            if ($existe) {
                throw ValidationException::withMessages([
                    'nif' => ['O NIF já existe.'],
                ]);
            }
        }

        // Verifica se a validação VIES está ativada na configuração (.env)
        $pais = Pais::find($request->pais_id);
        if (config('vies.enabled') && $pais && in_array($pais->codigo, ['AT','BE','BG','CY','CZ','DE','DK','EE','EL','ES','FI','FR','HR','HU','IE','IT','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK'])) {
            // Se estiver ativada, realiza a validação do NIF com o serviço VIES
            $vies = new VatValidationService();
            $isValid = $vies->validarNifComVies($request->nif, $pais->codigo);

            if (!$isValid) {
                throw ValidationException::withMessages([
                    'nif' => ['O NIF não é válido segundo o VIES.'],
                ]);
            }
        }

        // Merge o novo hash de NIF para atualização
        $request->merge([
            'nif_hash' => $nifHash,
        ]);

        $entidade->update($request->only([
            'tipo',
            'numero',
            'nif',
            'nome',
            'morada',
            'codigo_postal',
            'localidade',
            'pais_id',
            'telefone',
            'telemovel',
            'website',
            'email',
            'consentimento_rgpd',
            'observacoes',
            'estado',
        ]));

        activity()
            ->useLog('Entidades')
            ->performedOn($entidade)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Atualizou a entidade: ' . $entidade->nome . ' (Nº ' . $entidade->numero . ')');

        if ($entidade->tipo === 'cliente') {
            return redirect()->route('clientes.index')->with('success', 'Entidade atualizada com sucesso.');
        } elseif ($entidade->tipo === 'fornecedor') {
            return redirect()->route('fornecedores.index')->with('success', 'Entidade atualizada com sucesso.');
        }

        return redirect()->route('entidades.index')->with('success', 'Entidade atualizada com sucesso.');
    }

    public function destroy(Entidade $entidade)
    {
        $tipo = $entidade->tipo;
        $entidade->delete();

        activity()
            ->useLog('Entidades')
            ->performedOn($entidade)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
        ->log('Eliminou a entidade: ' . $entidade->nome . ' (Nº ' . $entidade->numero . ')');

        return redirect()->route($tipo === 'cliente' ? 'clientes.index' : 'fornecedores.index')
            ->with('success', 'Entidade eliminada com sucesso.');
    }
}
