<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinhaEncomenda extends Model
{
    protected $table = 'linhas_encomendas';

    protected $fillable = [
        'encomenda_id',
        'artigo_id',
        'fornecedor_id',
        'quantidade',
        'preco_unitario',
    ];

    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
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
