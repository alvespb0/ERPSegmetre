<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ArquivoRemessa extends Model
{
    use SoftDeletes;

    protected $table = 'arquivo_remessa';

    protected $fillable = [
        'configuracao_cobranca_id',
        'numero_remessa',
        'nome_arquivo',
        'path',
        'status',
        'data_geracao',
        'data_envio',
    ];

    public function configuracaoCobranca(){
        return $this->belongsTo(ConfiguracaoCobranca::class, 'configuracao_cobranca_id');
    }

    public function boletos(){
        return $this->hasMany(BoletoCobranca::class, 'arquivo_remessa_id');
    }

    public static function gerarProximoNumeroRemessa(int $configuracaoCobrancaId): int {

        $ultimoNumero = self::max('numero_remessa');

        if ($ultimoNumero) {
            return $ultimoNumero + 1;
        }

        return ConfiguracaoCobranca::findOrFail(
            $configuracaoCobrancaId
        )->numero_inicial_cobranca;
    }
}
