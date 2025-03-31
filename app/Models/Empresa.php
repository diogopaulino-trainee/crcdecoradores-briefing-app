<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'logotipo',
        'nome',
        'morada',
        'codigo_postal',
        'localidade',
        'numero_contribuinte',
    ];

    // Encriptação dos campos sensíveis
    protected $casts = [
        'morada' => 'encrypted',
        'codigo_postal' => 'encrypted',
        'localidade' => 'encrypted',
        'numero_contribuinte' => 'encrypted',
    ];
}
