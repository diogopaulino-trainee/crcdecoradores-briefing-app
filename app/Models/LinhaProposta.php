<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinhaProposta extends Model
{
    protected $table = 'linhas_propostas';

    protected $fillable = [
        'proposta_id',
        'artigo_id',
        'fornecedor_id',
        'quantidade',
        'preco_unitario',
    ];

    public function proposta()
    {
        return $this->belongsTo(Proposta::class);
    }

    public function artigo()
    {
        return $this->belongsTo(Artigo::class);
    }

    public function fornecedor()
    {
        return $this->belongsTo(Entidade::class, 'fornecedor_id');
    }
}
