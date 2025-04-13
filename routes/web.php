<?php

use App\Http\Controllers\ArtigoController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\FaturaFornecedorController;
use App\Http\Controllers\FicheiroSeguroController;
use App\Http\Controllers\FuncaoController;
use App\Http\Controllers\IvaController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\OrdemTrabalhoController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\PermissaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\UtilizadorController;
use App\Models\Empresa;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'twoFactorEnabled' => Auth::user()->two_factor_confirmed_at !== null,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/empresa/logotipo', function () {
    $empresa = \App\Models\Empresa::first();

    if ($empresa && $empresa->logotipo && Storage::disk('private')->exists($empresa->logotipo)) {
        return response()->file(storage_path('app/private/' . $empresa->logotipo));
    }

    abort(404);
})->name('empresa.logotipo');

Route::middleware('auth')->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Utilizadores
    Route::get('/utilizadores', [UtilizadorController::class, 'index'])->name('utilizadores.index')->middleware('can:utilizadores.view');
    Route::get('/utilizadores/create', [UtilizadorController::class, 'create'])->name('utilizadores.create')->middleware('can:utilizadores.create');
    Route::post('/utilizadores', [UtilizadorController::class, 'store'])->name('utilizadores.store')->middleware('can:utilizadores.create');
    Route::get('/utilizadores/{utilizador}', [UtilizadorController::class, 'show'])->name('utilizadores.show')->middleware('can:utilizadores.view');
    Route::get('/utilizadores/{utilizador}/edit', [UtilizadorController::class, 'edit'])->name('utilizadores.edit')->middleware('can:utilizadores.edit');
    Route::put('/utilizadores/{utilizador}', [UtilizadorController::class, 'update'])->name('utilizadores.update')->middleware('can:utilizadores.edit');
    Route::delete('/utilizadores/{utilizador}', [UtilizadorController::class, 'destroy'])->name('utilizadores.destroy')->middleware('can:utilizadores.delete');

    // Permissões
    Route::get('/permissoes', [PermissaoController::class, 'index'])->name('permissoes.index')->middleware('can:permissoes.view');
    Route::get('/permissoes/create', [PermissaoController::class, 'create'])->name('permissoes.create')->middleware('can:permissoes.create');
    Route::post('/permissoes', [PermissaoController::class, 'store'])->name('permissoes.store')->middleware('can:permissoes.create');
    Route::get('/permissoes/{role}', [PermissaoController::class, 'show'])->name('permissoes.show')->middleware('can:permissoes.view');
    Route::get('/permissoes/{role}/edit', [PermissaoController::class, 'edit'])->name('permissoes.edit')->middleware('can:permissoes.edit');
    Route::put('/permissoes/{role}', [PermissaoController::class, 'update'])->name('permissoes.update')->middleware('can:permissoes.edit');
    Route::delete('/permissoes/{role}', [PermissaoController::class, 'destroy'])->name('permissoes.destroy')->middleware('can:permissoes.delete');

    // Entidades (clientes e fornecedores)
    Route::get('/clientes', [EntidadeController::class, 'clientes'])->name('clientes.index')->middleware('can:entidades.view');
    Route::get('/fornecedores', [EntidadeController::class, 'fornecedores'])->name('fornecedores.index')->middleware('can:entidades.view');
    Route::get('/entidades', [EntidadeController::class, 'index'])->name('entidades.index')->middleware('can:entidades.view');
    Route::get('/entidades/create', [EntidadeController::class, 'create'])->name('entidades.create')->middleware('can:entidades.create');
    Route::post('/entidades', [EntidadeController::class, 'store'])->name('entidades.store')->middleware('can:entidades.create');
    Route::get('/entidades/{entidade}', [EntidadeController::class, 'show'])->name('entidades.show')->middleware('can:entidades.view');
    Route::get('/entidades/{entidade}/edit', [EntidadeController::class, 'edit'])->name('entidades.edit')->middleware('can:entidades.edit');
    Route::put('/entidades/{entidade}', [EntidadeController::class, 'update'])->name('entidades.update')->middleware('can:entidades.edit');
    Route::delete('/entidades/{entidade}', [EntidadeController::class, 'destroy'])->name('entidades.destroy')->middleware('can:entidades.delete');

    // Contactos
    Route::get('/contactos', [ContactoController::class, 'index'])->name('contactos.index')->middleware('can:contactos.view');
    Route::get('/contactos/create', [ContactoController::class, 'create'])->name('contactos.create')->middleware('can:contactos.create');
    Route::post('/contactos', [ContactoController::class, 'store'])->name('contactos.store')->middleware('can:contactos.create');
    Route::get('/contactos/{contacto}', [ContactoController::class, 'show'])->name('contactos.show')->middleware('can:contactos.view');
    Route::get('/contactos/{contacto}/edit', [ContactoController::class, 'edit'])->name('contactos.edit')->middleware('can:contactos.edit');
    Route::put('/contactos/{contacto}', [ContactoController::class, 'update'])->name('contactos.update')->middleware('can:contactos.edit');
    Route::delete('/contactos/{contacto}', [ContactoController::class, 'destroy'])->name('contactos.destroy')->middleware('can:contactos.delete');

    // Propostas
    Route::get('/propostas', [PropostaController::class, 'index'])->name('propostas.index')->middleware('can:propostas.view');
    Route::get('/propostas/create', [PropostaController::class, 'create'])->name('propostas.create')->middleware('can:propostas.create');
    Route::post('/propostas', [PropostaController::class, 'store'])->name('propostas.store')->middleware('can:propostas.create');
    Route::get('/propostas/{proposta}', [PropostaController::class, 'show'])->name('propostas.show')->middleware('can:propostas.view');
    Route::get('/propostas/{proposta}/edit', [PropostaController::class, 'edit'])->name('propostas.edit')->middleware('can:propostas.edit');
    Route::put('/propostas/{proposta}', [PropostaController::class, 'update'])->name('propostas.update')->middleware('can:propostas.edit');
    Route::delete('/propostas/{proposta}', [PropostaController::class, 'destroy'])->name('propostas.destroy')->middleware('can:propostas.delete');
    Route::get('/propostas/{proposta}/pdf', [PropostaController::class, 'download'])->name('propostas.download')->middleware('can:propostas.view');
    Route::post('/propostas/{proposta}/converter', [PropostaController::class, 'converter'])->name('propostas.converter')->middleware('can:propostas.edit');

    // Encomendas
    Route::get('/encomendas/clientes', [EncomendaController::class, 'clientes'])->name('encomendas.clientes')->middleware('can:encomendas.view');
    Route::get('/encomendas/fornecedores', [EncomendaController::class, 'fornecedores'])->name('encomendas.fornecedores')->middleware('can:encomendas.view');
    Route::get('/encomendas', [EncomendaController::class, 'index'])->name('encomendas.index')->middleware('can:encomendas.view');
    Route::get('/encomendas/create', [EncomendaController::class, 'create'])->name('encomendas.create')->middleware('can:encomendas.create');
    Route::post('/encomendas', [EncomendaController::class, 'store'])->name('encomendas.store')->middleware('can:encomendas.create');
    Route::get('/encomendas/{encomenda}', [EncomendaController::class, 'show'])->name('encomendas.show')->middleware('can:encomendas.view');
    Route::get('/encomendas/{encomenda}/edit', [EncomendaController::class, 'edit'])->name('encomendas.edit')->middleware('can:encomendas.edit');
    Route::put('/encomendas/{encomenda}', [EncomendaController::class, 'update'])->name('encomendas.update')->middleware('can:encomendas.edit');
    Route::delete('/encomendas/{encomenda}', [EncomendaController::class, 'destroy'])->name('encomendas.destroy')->middleware('can:encomendas.delete');
    Route::post('/encomendas/{encomenda}/converter', [EncomendaController::class, 'converter'])->name('encomendas.converter')->middleware('can:encomendas.edit');
    Route::get('/encomendas/{encomenda}/pdf', [EncomendaController::class, 'download'])->name('encomendas.download')->middleware('can:encomendas.view');

    // Ordens de Trabalho
    Route::get('/ordens-trabalho', [OrdemTrabalhoController::class, 'index'])->name('ordens-trabalho.index')->middleware('can:ordens-trabalho.view');
    Route::get('/ordens-trabalho/create', [OrdemTrabalhoController::class, 'create'])->name('ordens-trabalho.create')->middleware('can:ordens-trabalho.create');
    Route::post('/ordens-trabalho', [OrdemTrabalhoController::class, 'store'])->name('ordens-trabalho.store')->middleware('can:ordens-trabalho.create');
    Route::get('/ordens-trabalho/{ordemTrabalho}', [OrdemTrabalhoController::class, 'show'])->name('ordens-trabalho.show')->middleware('can:ordens-trabalho.view');
    Route::get('/ordens-trabalho/{ordemTrabalho}/edit', [OrdemTrabalhoController::class, 'edit'])->name('ordens-trabalho.edit')->middleware('can:ordens-trabalho.edit');
    Route::put('/ordens-trabalho/{ordemTrabalho}', [OrdemTrabalhoController::class, 'update'])->name('ordens-trabalho.update')->middleware('can:ordens-trabalho.edit');
    Route::delete('/ordens-trabalho/{ordemTrabalho}', [OrdemTrabalhoController::class, 'destroy'])->name('ordens-trabalho.destroy')->middleware('can:ordens-trabalho.delete');

    // Faturas de Fornecedor
    Route::get('/faturas-fornecedores', [FaturaFornecedorController::class, 'index'])->name('faturas.index')->middleware('can:faturas.view');
    Route::get('/faturas-fornecedores/create', [FaturaFornecedorController::class, 'create'])->name('faturas.create')->middleware('can:faturas.create');
    Route::post('/faturas-fornecedores', [FaturaFornecedorController::class, 'store'])->name('faturas.store')->middleware('can:faturas.create');
    Route::get('/faturas-fornecedores/{faturaFornecedor}', [FaturaFornecedorController::class, 'show'])->name('faturas.show')->middleware('can:faturas.view');
    Route::get('/faturas-fornecedores/{faturaFornecedor}/edit', [FaturaFornecedorController::class, 'edit'])->name('faturas.edit')->middleware('can:faturas.edit');
    Route::put('/faturas-fornecedores/{faturaFornecedor}', [FaturaFornecedorController::class, 'update'])->name('faturas.update')->middleware('can:faturas.edit');
    Route::delete('/faturas-fornecedores/{faturaFornecedor}', [FaturaFornecedorController::class, 'destroy'])->name('faturas.destroy')->middleware('can:faturas.delete');

    // Empresa
    Route::get('/empresa', [EmpresaController::class, 'index'])->name('empresa.index')->middleware('can:empresa.view');
    Route::post('/empresa/update', [EmpresaController::class, 'update'])->name('empresa.update')->middleware('can:empresa.edit');

    // Artigos
    Route::get('/artigos', [ArtigoController::class, 'index'])->name('artigos.index')->middleware('can:artigos.view');
    Route::get('/artigos/create', [ArtigoController::class, 'create'])->name('artigos.create')->middleware('can:artigos.create');
    Route::post('/artigos', [ArtigoController::class, 'store'])->name('artigos.store')->middleware('can:artigos.create');
    Route::get('/artigos/{artigo}', [ArtigoController::class, 'show'])->name('artigos.show')->middleware('can:artigos.view');
    Route::get('/artigos/{artigo}/edit', [ArtigoController::class, 'edit'])->name('artigos.edit')->middleware('can:artigos.edit');
    Route::put('/artigos/{artigo}', [ArtigoController::class, 'update'])->name('artigos.update')->middleware('can:artigos.edit');
    Route::delete('/artigos/{artigo}', [ArtigoController::class, 'destroy'])->name('artigos.destroy')->middleware('can:artigos.delete');

    // IVAs
    Route::get('/ivas', [IvaController::class, 'index'])->name('ivas.index')->middleware('can:ivas.view');
    Route::get('/ivas/create', [IvaController::class, 'create'])->name('ivas.create')->middleware('can:ivas.create');
    Route::post('/ivas', [IvaController::class, 'store'])->name('ivas.store')->middleware('can:ivas.create');
    Route::get('/ivas/{iva}', [IvaController::class, 'show'])->name('ivas.show')->middleware('can:ivas.view');
    Route::get('/ivas/{iva}/edit', [IvaController::class, 'edit'])->name('ivas.edit')->middleware('can:ivas.edit');
    Route::put('/ivas/{iva}', [IvaController::class, 'update'])->name('ivas.update')->middleware('can:ivas.edit');
    Route::delete('/ivas/{iva}', [IvaController::class, 'destroy'])->name('ivas.destroy')->middleware('can:ivas.delete');

    // Funções
    Route::get('/funcoes', [FuncaoController::class, 'index'])->name('funcoes.index')->middleware('can:funcoes.view');
    Route::get('/funcoes/create', [FuncaoController::class, 'create'])->name('funcoes.create')->middleware('can:funcoes.create');
    Route::post('/funcoes', [FuncaoController::class, 'store'])->name('funcoes.store')->middleware('can:funcoes.create');
    Route::get('/funcoes/{funcao}', [FuncaoController::class, 'show'])->name('funcoes.show')->middleware('can:funcoes.view');
    Route::get('/funcoes/{funcao}/edit', [FuncaoController::class, 'edit'])->name('funcoes.edit')->middleware('can:funcoes.edit');
    Route::put('/funcoes/{funcao}', [FuncaoController::class, 'update'])->name('funcoes.update')->middleware('can:funcoes.edit');
    Route::delete('/funcoes/{funcao}', [FuncaoController::class, 'destroy'])->name('funcoes.destroy')->middleware('can:funcoes.delete');

    // Países
    Route::get('/paises', [PaisController::class, 'index'])->name('paises.index')->middleware('can:paises.view');
    Route::get('/paises/create', [PaisController::class, 'create'])->name('paises.create')->middleware('can:paises.create');
    Route::post('/paises', [PaisController::class, 'store'])->name('paises.store')->middleware('can:paises.create');
    Route::get('/paises/{pais}', [PaisController::class, 'show'])->name('paises.show')->middleware('can:paises.view');
    Route::get('/paises/{pais}/edit', [PaisController::class, 'edit'])->name('paises.edit')->middleware('can:paises.edit');
    Route::put('/paises/{pais}', [PaisController::class, 'update'])->name('paises.update')->middleware('can:paises.edit');
    Route::delete('/paises/{pais}', [PaisController::class, 'destroy'])->name('paises.destroy')->middleware('can:paises.delete');

    // Logs
    Route::get('logs', [LogController::class, 'index'])->name('logs.index')->middleware('can:logs.view');
    Route::get('logs/{id}', [LogController::class, 'show'])->name('logs.show')->middleware('can:logs.view');
    Route::delete('logs/{id}', [LogController::class, 'destroy'])->name('logs.destroy')->middleware('can:logs.delete');
    Route::post('logs/clear', [LogController::class, 'clearAll'])->name('logs.clearAll')->middleware('can:logs.delete');

    // Ficheiros protegidos
    Route::get('/ficheiros/privado/{caminho}', [FicheiroSeguroController::class, 'ver'])
        ->where('caminho', '.*')
        ->name('ficheiro.privado');
});

require __DIR__.'/auth.php';