<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = [
        'nome',
        'codigo',
    ];

    public function entidades()
    {
        return $this->hasMany(Entidade::class);
    }
}
