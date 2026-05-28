<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ArquivoRetorno extends Model
{
    use SoftDeletes;

    protected $table = 'arquivo_retorno';

    protected $fillable = [
        'configuracao_cobranca_id',
        'numero_retorno',
        'nome_arquivo',
        'path',
        'status',
        'data_processamento',
    ];

    public function configuracaoCobranca(){
        return $this->belongsTo(ConfiguracaoCobranca::class, 'configuracao_cobranca_id');
    }

    public function boletos(){
        return $this->hasMany(BoletoCobranca::class, 'arquivo_retorno_id');
    }
}
