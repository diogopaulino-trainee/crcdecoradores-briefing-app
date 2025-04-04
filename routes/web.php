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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropostaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/clientes', [EntidadeController::class, 'clientes'])->name('clientes.index');
    Route::get('/fornecedores', [EntidadeController::class, 'fornecedores'])->name('fornecedores.index');
    Route::resource('entidades', EntidadeController::class);
    Route::resource('contactos', ContactoController::class);
    Route::resource('propostas', PropostaController::class);
    Route::get('/propostas/{proposta}/pdf', [PropostaController::class, 'download'])->name('propostas.download');
    Route::get('/encomendas/clientes', [EncomendaController::class, 'clientes'])->name('encomendas.clientes');
    Route::get('/encomendas/fornecedores', [EncomendaController::class, 'fornecedores'])->name('encomendas.fornecedores');
    Route::resource('encomendas', EncomendaController::class);
    Route::resource('ordens-trabalho', OrdemTrabalhoController::class);
    Route::resource('faturas-fornecedores', FaturaFornecedorController::class)->names([
        'index' => 'faturas.index',
        'create' => 'faturas.create',
        'store' => 'faturas.store',
        'show' => 'faturas.show',
        'edit' => 'faturas.edit',
        'update' => 'faturas.update',
        'destroy' => 'faturas.destroy',
    ]);
    Route::resource('empresas', EmpresaController::class);
    Route::resource('artigos', ArtigoController::class);
    Route::resource('ivas', IvaController::class);
    Route::resource('funcoes', FuncaoController::class);
    Route::resource('paises', PaisController::class);

    Route::get('/ficheiros/privado/{caminho}', [FicheiroSeguroController::class, 'ver'])
        ->where('caminho', '.*')
        ->name('ficheiro.privado');

    // Logs
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('logs/{id}', [LogController::class, 'show'])->name('logs.show');
    Route::delete('logs/{id}', [LogController::class, 'destroy'])->name('logs.destroy');
    Route::post('logs/clear', [LogController::class, 'clearAll'])->name('logs.clearAll');
});

require __DIR__.'/auth.php';
