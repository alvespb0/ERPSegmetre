<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimentacao extends BaseModel
{
    protected $table = 'movimentacoes';

    protected $fillable = [
        'forma_pagamento_id', #nullable
        'parcela_id',
        'conta_id', #nullable
        'valor_pago',
        'data_pagamento',
    ];

    public function formaPagamento(){
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }

    public function anexos(){
        return $this->morphMany(Anexo::class, 'anexavel');
    }

    public function parcela(){
        return $this->belongsTo(Parcela::class, 'parcela_id');
    }
    
    public function conta(){
        return $this->belongsTo(Conta::class, 'conta_id');
    }

}
