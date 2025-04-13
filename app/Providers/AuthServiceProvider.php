<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Throwable;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        Gate::before(function ($user, $ability) {
            $rolesAtivas = $user->roles()->where('estado', 'Ativo')->pluck('id');
        
            if ($rolesAtivas->isEmpty()) {
                Log::info("Acesso BLOQUEADO - utilizador sem roles ativas.");
                return false;
            }
        
            foreach ($rolesAtivas as $roleId) {
                if (\Spatie\Permission\Models\Role::find($roleId)?->hasPermissionTo($ability)) {
                    return true;
                }
            }
        
            return null;
        });

        try {
            Permission::all()->each(function ($permission) {
                Gate::define($permission->name, function ($user) use ($permission) {
                    $rolesAtivas = $user->roles()->where('estado', 'Ativo')->get();
            
                    foreach ($rolesAtivas as $role) {
                        if ($role->hasPermissionTo($permission)) {
                            return true;
                        }
                    }
            
                    return false;
                });
            });
        } catch (Throwable $e) {
            Log::error("Erro ao definir gates: " . $e->getMessage());
        }
    }
}
