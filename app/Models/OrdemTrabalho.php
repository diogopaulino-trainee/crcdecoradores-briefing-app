<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemTrabalho extends Model
{
    use HasFactory;

    protected $table = 'ordens_trabalho';

    protected $fillable = [
        'numero',
        'data_da_ordem',
        'entidade_id',
        'descricao',
        'estado',
    ];

    public function entidade()
    {
        return $this->belongsTo(Entidade::class);
    }
}
