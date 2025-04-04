<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    use HasFactory;

    protected $table = 'propostas';

    protected $fillable = [
        'numero',
        'data_da_proposta',
        'cliente_id',
        'validade',
        'estado',
        'valor_total',
    ];

    public function cliente()
    {
        return $this->belongsTo(Entidade::class, 'cliente_id');
    }

    public function linhas()
    {
        return $this->hasMany(LinhaProposta::class);
    }
}
