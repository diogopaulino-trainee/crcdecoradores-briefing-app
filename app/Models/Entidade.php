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
        'nif_hash',
        'nome',
        'morada',
        'codigo_postal',
        'localidade',
        'pais_id',
        'telefone',
        'telemovel',
        'website',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'estado',
    ];

    protected static function booted()
    {
        static::saving(function ($entidade) {
            // Gera o hash SHA-256 do NIF em texto plano
            $entidade->nif_hash = hash('sha256', $entidade->nif);
        });
    }

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

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }
}
