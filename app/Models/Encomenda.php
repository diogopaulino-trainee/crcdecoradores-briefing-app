<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encomenda extends Model
{
    use HasFactory;

    protected $table = 'encomendas';

    protected $fillable = [
        'tipo',
        'numero',
        'data_da_proposta',
        'cliente_id',
        'estado',
        'valor_total',
    ];

    public function cliente()
    {
        return $this->belongsTo(Entidade::class, 'cliente_id');
    }
}
