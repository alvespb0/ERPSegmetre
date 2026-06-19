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
        'pdf_path',
        'sequencial_boleto',
        'numero_documento',
        'modalidade',          # 01=simples, 03=caucionada, 04=vinculada, 05=carnê
        'info_complementares',
        'especie_documento',   # DM=duplicata mercantil, DS=serviço, NP=promissória, etc.
        'linha_digitavel',
        'codigo_barras',
        'status',             # pendente, remetido, registrado, liquidado, baixado, rejeitado, cancelado
        'codigo_multa',       # 0=isento, 1=valor fixo, 2=percentual
        'codigo_juros',       # 0=isento, 1=valor por dia, 2=taxa mensal
        'codigo_protesto',    # 1=dias corridos, 2=dias úteis, 3=não protestar, 8=negativação, 9=automática
        'valor_multa',
        'valor_juros',
        'data_registro',
        'data_multa',
        'data_juro',
        'data_liquidacao',
        'prazo_protesto',
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

    /**
     * Retorna o próximo sequencial interno do sistema.
     * Considera registros excluídos (soft delete) para evitar reutilização.
     */
    public static function proximoSequencial(): int
    {
        $ultimoSequencial = static::withTrashed()->max('sequencial_boleto');

        return $ultimoSequencial !== null
            ? (int) $ultimoSequencial + 1
            : 1;
    }

    /**
     * Gera o número do documento interno do ERP.
     * Padrão: RRRR-SEQUENCIAL-YYYYMMDD (ex.: 4829-12-20260618)
     */
    public static function gerarNumeroDocumento(int $sequencial, ?Carbon $data = null): string
    {
        $data ??= now();

        $aleatorio = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return sprintf('%s-%d-%s', $aleatorio, $sequencial, $data->format('Ymd'));
    }
}
