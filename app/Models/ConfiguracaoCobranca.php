<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoCobranca extends BaseModel
{
    use SoftDeletes;

    protected $table = 'configuracao_cobranca';

    protected $fillable = [
        'conta_id',
        'empresa_parametro_id',
        'integracao_id',
        'codigo_cedente',
        'codigo_juros',
        'valor_juros',
        'dias_inicio_juros',
        'codigo_multa',
        'valor_multa',
        'dias_inicio_multa',
        'dias_limite_pagamento',
        'carteira',
        'layout_cnab',
        'ambiente',
        'numero_inicial_cobranca'
    ];

    protected $casts = [
        'valor_juros' => 'decimal:4',
        'valor_multa' => 'decimal:2',
    ];

    public function conta(){
        return $this->belongsTo(Conta::class, 'conta_id');
    }

    public function empresaParametro(){
        return $this->belongsTo(EmpresaParametro::class, 'empresa_parametro_id');
    }

    public function integracao(){
        return $this->belongsTo(Integracao::class, 'integracao_id');
    }

}
