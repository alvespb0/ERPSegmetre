<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movimentacao extends Model
{
    protected $table = 'movimentacoes';

    use SoftDeletes;

    protected $fillable = [
        'forma_pagamento_id', #nullable
        'parcela_id',
        'valor_pago',
        'data_pagamento',
    ];

    public function formaPagamento(){
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }

    public function parcela(){
        return $this->belongsTo(Parcela::class, 'parcela_id');
    }
}
