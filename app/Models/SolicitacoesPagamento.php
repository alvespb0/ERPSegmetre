<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitacoesPagamento extends BaseModel
{
    protected $table = 'solicitacoes_pagamento';

    protected $fillable = [
        'parcela_id',
        'movimentacao_id',
        'empresa_parametro_id',
        'chave_idempotente',
        'tipo',
        'identificador',
        'valor',
        'data_solicitacao',
        'data_pagamento',
        'comprovante_path',
        'status',
    ];

    /**
     * Parcela vinculada à solicitação.
     */
    public function parcela()
    {
        return $this->belongsTo(Parcela::class);
    }

    /**
     * Movimentação financeira gerada pelo pagamento.
     */
    public function movimentacao()
    {
        return $this->belongsTo(Movimentacao::class);
    }

    /**
     * Parâmetro da empresa utilizado na solicitação.
     */
    public function empresaParametro()
    {
        return $this->belongsTo(EmpresaParametro::class);
    }

}
