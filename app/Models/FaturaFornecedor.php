<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaturaFornecedor extends Model
{
    use HasFactory;

    protected $table = 'faturas_fornecedores';

    protected $fillable = [
        'numero',
        'data_da_fatura',
        'data_de_vencimento',
        'fornecedor_id',
        'encomenda_fornecedor_id',
        'valor_total',
        'documento',
        'comprovativo_pagamento',
        'estado',
    ];

    public function fornecedor()
    {
        return $this->belongsTo(Entidade::class, 'fornecedor_id');
    }
    
    public function encomendaFornecedor()
    {
        return $this->belongsTo(Encomenda::class, 'encomenda_fornecedor_id');
    }
}
