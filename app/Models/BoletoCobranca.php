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
        'numero_documento',
        'linha_digitavel',
        'codigo_barras',
        'status',
        'valor_multa',
        'valor_juros',
        'data_remessa',
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

    public static function gerarProximoNossoNumero(int $configuracaoCobrancaId): string {
        $configuracao = ConfiguracaoCobranca::findOrFail(
            $configuracaoCobrancaId
        );

        $ultimoBoleto = self::query()
            ->where(
                'configuracao_cobranca_id',
                $configuracaoCobrancaId
            )
            ->orderByRaw('CAST(nosso_numero as UNSIGNED) DESC')
            ->first();

        if (!$ultimoBoleto) {
            return (string) $configuracao->numero_inicial_cobranca;
        }

        return (string) (
            ((int) $ultimoBoleto->nosso_numero) + 1
        );
    }

}
