<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iva extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'percentagem',
    ];

    protected $casts = [
        'percentagem' => 'float',
    ];

    public function artigos()
    {
        return $this->hasMany(Artigo::class);
    }
}
