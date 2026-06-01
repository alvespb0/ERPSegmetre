<?php
namespace App\Bancos\Sicoob\Cnab240;

use App\Models\Conta;
use App\Models\ArquivoRemessa;
use Carbon\Carbon;

class SegmentoP
{
    public $conta;
    public $numeroRegistroP;
    public $numeroLote;
    public $codigoMovimentacao;
    public $configuracao;

    public function __construct(Conta $conta, $numeroLote, $numeroRegistroP, $codigoMovimentacao){
        $this->conta = $conta;
        $this->numeroLote = $numeroLote;
        $this->numeroRegistroP = $numeroRegistroP;
        $this->codigoMovimentacao = $codigoMovimentacao;
        $this->configuracao = $conta->configuracaoCobranca;
    }

    public function montar(): string {
        $agencia = $this->separarDv($this->conta->agencia); # array
        $contaBancaria = $this->separarDv($this->conta->conta); # array

        $pos01_03 = Cnab240Formatter::numerico($this->conta->banco->numero_banco, 3); # Codigo do banco; tipo numerico;
        $pos04_07 = Cnab240Formatter::numerico($this->numeroLote, 4); # Lote do serviço, o mesmo só é sequencial dentro do mesmo arquivo (tem que ser o mesmo do headerLote); tipo numerico
        $pos08_08 = Cnab240Formatter::numerico("3", 1); # Tipo de registro, hardcodado, sempre será 3; tipo numerico
        $pos09_13 = Cnab240Formatter::numerico($this->numeroRegistroP, 5); # Numero sequencial do registro P do lote, sequencial dentro do mesmo arquivo (ou seja, 1 remessa com 3 movimentações de segmento P 1, 2, 3); tipo numerico
        $pos14_14 = Cnab240Formatter::alfa("P", 1); # Cod segmento do registro detalhe: P; tipo alfa
        $pos15_15 = Cnab240Formatter::alfa("", 1); # Uso exclusivo FEBRABAN; tipo alfa
        $pos16_17 = Cnab240Formatter::numerico($this->codigoMovimentacao, 2); # Codigo de movimentação da remessa, é tratado (se realmente existe, etc) em uma camada acima; tipo numerico
        $pos18_22 = Cnab240Formatter::numerico($agencia['num'], 5); # Prefixo da cooperativa; tipo numerico
        $pos23_23 = Cnab240Formatter::alfa($agencia['dv'], 1); # Digito verificador da agencia; tipo alfa
        $pos24_35 = Cnab240Formatter::numerico($contaBancaria['num'], 12);


        if (strlen($linha) !== 240) {
            throw new \Exception(
                'HeaderLote inválido. Tamanho: ' . strlen($linha)
            );
        }

        \Log::debug('Debug de header do lote cnab', ['length' => strlen($linha), 'linha' => $linha]);

        return $linha;
    }

    private function separarDv($valor): array{
        list($num, $dv) = explode('-', $valor);

        return [
            'num' => $num,
            'dv' => $dv
        ];
    }
}