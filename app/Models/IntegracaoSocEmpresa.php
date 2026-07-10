<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegracaoSocEmpresa extends Model
{
    protected $table = 'integracao_soc_empresas';

    protected $fillable = [
        'entidade_id',
        'codigo_empresa',
        'codigo_unidade',
        'nome_unidade',
    ];

    public function entidade(){
        return $this->belongsTo(Entidade::class);
    }
}