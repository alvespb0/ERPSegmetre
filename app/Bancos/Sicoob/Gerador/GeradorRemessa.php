<?php
namespace App\Bancos\Sicoob\Gerador;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

use App\Models\Conta;
use App\Models\ArquivoRemessa;
use App\Models\BoletoCobranca;

use App\Bancos\Sicoob\Cnab240\HeaderArquivo;
use App\Bancos\Sicoob\Cnab240\HeaderLote;
use App\Bancos\Sicoob\Cnab240\SegmentoP;
use App\Bancos\Sicoob\Cnab240\SegmentoQ;
use App\Bancos\Sicoob\Cnab240\TraillerLote;
use App\Bancos\Sicoob\Cnab240\TraillerArquivo;

class GeradorRemessa
{
    public $numeroRemessa;
    public $numeroLote;
    public $conta;
    public $codigoMovimentacao;
    public $numEspecie;

    public function __construct($numeroRemessa, $numeroLote, $codigoMovimentacao, $numEspecie, Conta $conta){
        $this->numeroRemessa = $numeroRemessa;
        $this->numeroLote = $numeroLote;
        $this->conta = $conta;
        $this->codigoMovimentacao = $codigoMovimentacao; # instanciado no construtor, não vou permitir ações em lote para movimentações diferentes, se for fazer 1 remessa, sempre vai ser com ou só entrada de titulo, ou só pedido de baixa
        $this->numEspecie = $numEspecie; # mesma logica do comentario acima
    }

    public function gerarRemessaCobrancaSimples(Collection $boletos){
        try{
            $linhas = [];

            /* HEADERS */
            $linhas[] = $this->montarHeaderArquivo();
            $linhas[] = $this->montarHeaderLote();

            /* SEGMENTOS */
            $numeroRegistro = 1;

            foreach($boletos as $boleto){
                $linhas[] = $this->montarSegmentoP($boleto, $numeroRegistro++);
                $linhas[] = $this->montarSegmentoQ($boleto, $numeroRegistro++);
            }

            /* TRAILLER LOTE */
            $qtdCobrancas = $boletos->count();
            $qtdRegistrosLote =
                1 +                 // Header Lote
                ($qtdCobrancas * 2) + // P + Q
                1;                  // Trailler Lote

            $valorCobrancas = $boletos->sum(function ($boleto) {
                return $boleto->parcela->valor ?? 0;
            });

            $linhas[] = $this->montarTraillerLote($qtdRegistrosLote, $qtdCobrancas, valorCobrancaSimples: $valorCobrancas);

            /* TRAILLER ARQUIVO */
            $qtdLotesArquivo = 1; # HARDCODADO, NÃO VEJO NECESSIDADE EM LÓGICA DE MAIS DE UM LOTE NO MOMENTO
            $qtdRegistrosArquivo =
                1 +                 // Header Arquivo
                1 +                 // Header Lote
                ($qtdCobrancas * 2) + // P + Q
                1 +                  // Trailler Lote
                1;                  // Trailler Arquivo
            $linhas[] = $this->montarTraillerArquivo($qtdLotesArquivo, $qtdRegistrosArquivo);

            /* MONTAR A REMESSA */
            $conteudo = implode("\r\n", $linhas) . "\r\n";

            $conteudo = mb_convert_encoding($conteudo, 'Windows-1252', 'UTF-8');

            $nomeArquivo = sprintf(
                'remessas/CB%s.rem',
                now()->format('YmdHis')
            );

            Storage::disk('local')->put($nomeArquivo, $conteudo);

            return $nomeArquivo;

        }catch(\Exception $e){
            \Log::error([
                    'Error' => 'Erro ao gerar o arquivo de remessa cnab 240',
                    'Message' => $e->getMessage()
                ]);

            return false;
        }
    }

    /**
     * Monta o registro de Header do Arquivo CNAB.
     *
     * Cria uma instância de {@see HeaderArquivo}, gera a linha do header
     * e valida se o tamanho final possui exatamente 240 caracteres.
     *
     * @throws \Exception Quando a linha gerada não possui 240 caracteres.
     *
     * @return string Retorna os dados do header do arquivo.
     */
    public function montarHeaderArquivo(): string{
         $headerArquivoConstructor = new HeaderArquivo($this->conta, $this->numeroRemessa);

         $linha = $headerArquivoConstructor->montar();

         if (strlen($linha) !== 240) {
             throw new \Exception(
                 'Header Lote inválido. Tamanho: ' . strlen($linha)
             );
         }

         return $linha;
    }

    /**
     * Monta o registro de Header do Lote CNAB.
     *
     * Cria uma instância de {@see HeaderLote}, gera a linha do header
     * e valida se o tamanho final possui exatamente 240 caracteres.
     *
     * @throws \Exception Quando a linha gerada não possui 240 caracteres.
     *
     * @return string Linha formatada do header do lote com 240 caracteres.
     */
    public function montarHeaderLote(): string{
        $headerLoteConstructor = new HeaderLote($this->conta, $this->numeroLote, $this->numeroRemessa);

        $linha = $headerLoteConstructor->montar();

        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Header Lote inválido. Tamanho: ' . strlen($linha)
            );
        }

        return $linha;
    }

    /**
     * Monta o registro do Segmento P do arquivo CNAB.
     *
     * Cria a instância responsável pela geração do Segmento P,
     * valida se a linha gerada possui exatamente 240 posições
     * e retorna o conteúdo formatado.
     *
     * @param BoletoCobranca $boleto Objeto contendo os dados do boleto.
     * @param int $numeroRegistroP Número sequencial do registro no lote.
     *
     * @return string Linha CNAB do Segmento P com 240 caracteres.
     *
     * @throws \Exception Quando a linha gerada não possuir 240 caracteres.
     */
    public function montarSegmentoP(BoletoCobranca $boleto, $numeroRegistroP): string{
        $segmentoPConstructor = new SegmentoP($this->conta, $boleto, $this->numeroLote, $numeroRegistroP, $this->codigoMovimentacao, $this->numEspecie);

        $linha = $segmentoPConstructor->montar();

        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Segmento P inválido. Tamanho: ' . strlen($linha)
            );
        }

        return $linha;
    }

    /**
     * Monta o registro do Segmento Q do arquivo CNAB.
     *
     * Cria a instância responsável pela geração do Segmento Q,
     * valida se a linha gerada possui exatamente 240 posições
     * e retorna o conteúdo formatado.
     *
     * @param BoletoCobranca $boleto Objeto contendo os dados do boleto.
     * @param int $numeroRegistroQ Número sequencial do registro no lote.
     *
     * @return string Linha CNAB do Segmento Q com 240 caracteres.
     *
     * @throws \Exception Quando a linha gerada não possuir 240 caracteres.
     */
    public function montarSegmentoQ(BoletoCobranca $boleto, $numeroRegistroQ): string{
        $segmentoQConstructor = new SegmentoQ($this->conta, $boleto, $this->numeroLote, $numeroRegistroQ, $this->codigoMovimentacao);

        $linha = $segmentoQConstructor->montar();

        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Segmento Q inválido. Tamanho: ' . strlen($linha)
            );
        }

        return $linha;
    }

    /**
     * Monta o registro Trailer do Lote CNAB.
     *
     * Gera o trailer do lote contendo os totais de registros, quantidades
     * e valores por modalidade de cobrança, validando se a linha final
     * possui exatamente 240 caracteres.
     *
     * @param int   $qtdRegistrosLote      Quantidade total de registros no lote.
     * @param int   $qtdCobrancaSimples    Quantidade de títulos de cobrança simples.
     * @param int   $qtdCobrancaVinculada  Quantidade de títulos de cobrança vinculada.
     * @param int   $qtdCobrancaCaucionada Quantidade de títulos de cobrança caucionada.
     * @param int   $qtdCobrancaDescontada Quantidade de títulos de cobrança descontada.
     * @param float $valorCobrancaSimples  Valor total da cobrança simples.
     * @param float $valorCobrancaVinculada Valor total da cobrança vinculada.
     * @param float $valorCobrancaCaucionada Valor total da cobrança caucionada.
     * @param float $valorCobrancaDescontada Valor total da cobrança descontada.
     *
     * @throws \Exception Quando a linha gerada não possui 240 caracteres.
     *
     * @return string Linha formatada do trailer do lote com 240 caracteres.
     */
    public function montarTraillerLote($qtdRegistrosLote, $qtdCobrancaSimples = 0, $qtdCobrancaVinculada = 0, $qtdCobrancaCaucionada = 0, $qtdCobrancaDescontada = 0,
                                $valorCobrancaSimples = 0, $valorCobrancaVinculada = 0, $valorCobrancaCaucionada = 0, $valorCobrancaDescontada = 0): string{

        $traillerLoteConstructor = new TraillerLote($this->conta, $this->numeroLote, $qtdRegistrosLote, $qtdCobrancaSimples, $qtdCobrancaVinculada, $qtdCobrancaCaucionada, $qtdCobrancaDescontada,
                                $valorCobrancaSimples, $valorCobrancaVinculada, $valorCobrancaCaucionada, $valorCobrancaDescontada);

        $linha = $traillerLoteConstructor->montar();

        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Trailler Lote inválido. Tamanho: ' . strlen($linha)
            );
        }

        return $linha;
    }

    /**
     * Monta o registro Trailer do Arquivo CNAB.
     *
     * Gera o trailer do arquivo contendo os totais gerais de lotes e
     * registros processados, validando se a linha final possui exatamente
     * 240 caracteres.
     *
     * @param int $qtdLotesArquivo     Quantidade total de lotes no arquivo.
     * @param int $qtdRegistrosArquivo Quantidade total de registros no arquivo.
     *
     * @throws \Exception Quando a linha gerada não possui 240 caracteres.
     *
     * @return string Linha formatada do trailer do arquivo com 240 caracteres.
     */
    public function montarTraillerArquivo($qtdLotesArquivo, $qtdRegistrosArquivo): string{
        $traillerArquivoConstructor = new TraillerArquivo($this->conta, $qtdLotesArquivo, $qtdRegistrosArquivo);
        $linha = $traillerArquivoConstructor->montar();

        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Trailler Arquivo inválido. Tamanho: ' . strlen($linha)
            );
        }

        return $linha;
    }
}