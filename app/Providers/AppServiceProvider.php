<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        // Força o uso de HTTPS em todas as URLs
        //URL::forceScheme('https');

        // Configura o prefetch do Vite com uma concorrência de 3
        Vite::prefetch(concurrency: 3);
    }
}
