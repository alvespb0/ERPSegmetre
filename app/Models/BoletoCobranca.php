<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BoletoCobranca extends Model
{
    use SoftDeletes;

    protected $table = 'boleto_cobranca';

    protected $fillable = [
        'parcela_id',
        'configuracao_cobranca_id',
        'arquivo_remessa_id',
        'arquivo_retorno_id',
        'nosso_numero',
        'modalidade',
        'especie_documento',
        'sequencial_boleto',
        'numero_documento',
        'linha_digitavel',
        'codigo_barras',
        'status',
        'codigo_juros', # 0 isento, 1 valor por dia, 2 taxa mensal
        'codigo_protesto', # 1 protestar dias corridos, 2 valor dias uteis, 3 não protestar, 8 negativação sem protesto, 9 negativação automática
        'valor_multa',
        'valor_juros',
        'prazo_protesto', # nullable dias após vencimento
        'data_registro',
        'data_liquidacao',
    ];

    public function parcela(){
        return $this->belongsTo(Parcela::class, 'parcela_id');
    }

    public function configuracaoCobranca(){
        return $this->belongsTo(ConfiguracaoCobranca::class, 'configuracao_cobranca_id');
    }

    public function arquivoRemessa(){
        return $this->belongsTo(ArquivoRemessa::class,'arquivo_remessa_id');
    }

    public function arquivoRetorno(){
        return $this->belongsTo(ArquivoRetorno::class,'arquivo_retorno_id');
    }

    public function getEstaLiquidadoAttribute(): bool{
        return $this->status === 'liquidado';
    }

    public function getEstaRegistradoAttribute(): bool{
        return in_array($this->status, [
            'registrado',
            'liquidado',
        ]);
    }

    public function getPodeGerarRemessaAttribute(): bool{
        return in_array($this->status, [
            'pendente',
            'rejeitado',
        ]);
    }
}
