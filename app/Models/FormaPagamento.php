<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormaPagamento extends BaseModel
{
    protected $table = 'forma_pagamento';

    use SoftDeletes;

    protected $fillable = [
        'nome' # unique
    ];

    public function movimentacoes(){
        return $this->hasMany(Movimentacao::class);
    }
}
