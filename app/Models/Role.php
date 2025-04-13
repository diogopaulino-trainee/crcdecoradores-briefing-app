<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'estado',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }
}
