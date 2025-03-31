<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidade extends Model
{
    use HasFactory;

    protected $table = 'entidades';

    protected $fillable = [
        'tipo',
        'numero',
        'nif',
        'nome',
        'morada',
        'codigo_postal',
        'localidade',
        'pais',
        'telefone',
        'telemovel',
        'website',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'estado',
    ];

    // Relação com os contactos
    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }

    // Encriptação dos campos sensíveis
    protected $casts = [
        'nif' => 'encrypted',
        'morada' => 'encrypted',
        'telefone' => 'encrypted',
        'telemovel' => 'encrypted',
        'observacoes' => 'encrypted',
    ];
}
