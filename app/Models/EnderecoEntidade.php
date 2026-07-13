<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnderecoEntidade extends BaseModel
{
    protected $table = 'endereco_entidade';

    protected $fillable = [
        'entidade_id',
        'rua',
        'bairro',
        'numero',
        'cep',
        'cidade',
        'uf',
        'complemento',
        'empresa_parametro_id',
    ];

    public function entidade(){
        return $this->belongsTo(Entidade::class, 'entidade_id');
    }
}
