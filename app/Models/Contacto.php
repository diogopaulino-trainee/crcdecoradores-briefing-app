<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    protected $table = 'contactos';

    protected $fillable = [
        'numero',
        'entidade_id',
        'primeiro_nome',
        'apelido',
        'funcao_id',
        'telefone',
        'telemovel',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'estado',
    ];

    // Relação com a entidade
    public function entidade()
    {
        return $this->belongsTo(Entidade::class);
    }

    public function funcao()
    {
        return $this->belongsTo(Funcao::class);
    }

    // Encriptação dos campos sensíveis
    protected $casts = [
        'telefone' => 'encrypted',
        'telemovel' => 'encrypted',
        'observacoes' => 'encrypted',
    ];
}
