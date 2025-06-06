<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telemovel',
        'estado',
        'funcao_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
    ];

    protected $guard_name = 'web';

    public function funcao()
    {
        return $this->belongsTo(Funcao::class);
    }

    public function isActive(): bool
    {
        return $this->estado === 'Ativo';
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $rolesAtivas = $this->roles()->where('estado', 'Ativo')->get();

        foreach ($rolesAtivas as $role) {
            if ($role->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }
}
