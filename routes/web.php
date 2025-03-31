<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\OrdemTrabalhoController;
use App\Http\Controllers\FaturaFornecedorController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ArtigoController;
use App\Http\Controllers\IvaController;
use App\Http\Controllers\FuncaoController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\LogController;

// Rota inicial
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'      => Route::has('login'),
        'canRegister'   => Route::has('register'),
        'laravelVersion'=> Application::VERSION,
        'phpVersion'    => PHP_VERSION,
    ]);
});

// Dashboard (acesso apenas para utilizadores autenticados)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas protegidas - apenas para utilizadores autenticados
Route::middleware('auth')->group(function () {
    // Perfil do Utilizador (exemplo do Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('entidades', EntidadeController::class);

    Route::resource('contactos', ContactoController::class);

    Route::resource('propostas', PropostaController::class);

    Route::resource('encomendas', EncomendaController::class);

    Route::resource('ordens-trabalho', OrdemTrabalhoController::class);

    Route::resource('faturas-fornecedores', FaturaFornecedorController::class);

    Route::resource('empresas', EmpresaController::class);

    Route::resource('artigos', ArtigoController::class);

    Route::resource('ivas', IvaController::class);

    Route::resource('funcoes', FuncaoController::class);

    Route::resource('paises', PaisController::class);

    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('logs/{id}', [LogController::class, 'show'])->name('logs.show');
    Route::delete('logs/{id}', [LogController::class, 'destroy'])->name('logs.destroy');
    Route::post('logs/clear', [LogController::class, 'clearAll'])->name('logs.clearAll');
});

require __DIR__.'/auth.php';
