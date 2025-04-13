<?php

namespace App\Providers;

use App\Models\Empresa;
use App\Models\Role;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Regista quaisquer serviços da aplicação.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa quaisquer serviços da aplicação.
     */
    public function boot(): void
    {
        app(PermissionRegistrar::class)->setRoleClass(Role::class);
        
        URL::forceScheme('https');
        Vite::prefetch(concurrency: 3);

        $empresa = \Illuminate\Support\Facades\Schema::hasTable('empresa') ? \App\Models\Empresa::first() : null;
        $defaultLogoPath = asset('logos/logo_crc.png');

        $logoPath = $empresa && $empresa->logotipo
            ? url('/empresa/logotipo')
            : $defaultLogoPath;

        View::composer('*', function ($view) use ($logoPath) {
            $view->with('logotipoEmpresa', $logoPath);
        });

        Inertia::share([
            'csrf_token' => fn () => csrf_token(),
            'auth' => fn () => [
                'user' => auth()->check()
                    ? auth()->user()->only([
                        'id',
                        'name',
                        'email',
                        'two_factor_confirmed_at',
                    ])
                    : null,
            ],
            'logotipoEmpresa' => fn () => $logoPath,
        ]);
    }
}
