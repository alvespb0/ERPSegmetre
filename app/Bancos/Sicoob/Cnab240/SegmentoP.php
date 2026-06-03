<?php
namespace App\Bancos\Sicoob\Cnab240;

use App\Models\Conta;
use App\Models\BoletoCobranca;
use App\Models\ArquivoRemessa;
use Carbon\Carbon;

class SegmentoP
{
    public Conta $conta;
    public BoletoCobranca $boleto;
    public $numeroRegistroP;
    public $numeroLote;
    public $codigoMovimentacao;
    public $numEspecie;
    public $configuracao;

    public function __construct(Conta $conta, BoletoCobranca $boleto, $numeroLote, $numeroRegistroP, $codigoMovimentacao, $numEspecie){
        $this->conta = $conta;
        $this->boleto = $boleto;
        $this->numeroLote = $numeroLote;
        $this->numeroRegistroP = $numeroRegistroP;
        $this->codigoMovimentacao = $codigoMovimentacao;
        $this->numEspecie = $numEspecie;
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
        $pos24_35 = Cnab240Formatter::numerico($contaBancaria['num'], 12); # Conta bancária; tipo numerico
        $pos36_36 = Cnab240Formatter::numerico($contaBancaria['dv'], 1); # Digito verificador da conta; tipo numerico
        $pos37_37 = Cnab240Formatter::alfa("", 1); # Digito verificador Ag/Conta, preencher com espaços em branco; tipo alfa
        $pos38_57 = Cnab240Formatter::alfa($this->boleto->nossoNumero, 20); # Nosso número, gerado devido ao boleto ser gerado pelo beneficiário, numero gerado na classe NossoNumeroService e armazenado na tabela de boleto; tipo alfa
        $pos58_58 = Cnab240Formatter::numerico($this->boleto->modalidade, 1); # Modalidade de cobrança; tipo numerico
        $pos59_59 = Cnab240Formatter::numerico("0", 1); # Forma de cadastro do titulo no banco, sempre 0; tipo numerico
        $pos60_60 = Cnab240Formatter::alfa("", 1); # Tipo de documento, preencher com espaços em branco; tipo alfa
        $pos61_61 = Cnab240Formatter::numerico("2", 1); # Emitente do boleto, sempre o beneficiário, então hardcodado 2; tipo numerico
        $pos62_62 = Cnab240Formatter::alfa("2", 1); # Identificação da distribuição do boleto (sicoob ou beneficiário), sempre beneficiário, hardcadado 2; tipo alfa
        $pos63_77 = Cnab240Formatter::alfa($this->boleto->sequencial_boleto, 15); # número de identificação do boleto, controlado pelo cliente; tipo alfa
        $pos78_85 = Cnab240Formatter::data($this->boleto->parcela->data_vencimento); # data de vencimento do boleto, sempre vinculado a parcela; tipo numerico
        $pos86_100 = Cnab240Formatter::valor($this->boleto->parcela->valor, 13); # valor nominal do titulo; tipo numerico
        $pos101_105 = Cnab240Formatter::numerico("00000", 5); # Agencia da cobrança, hardcodado 00000 conforme documentação; tipo numerico
        $pos106_106 = Cnab240Formatter::alfa("", 1); # DV da agencia, preencher com espaços em branco conforme documentação; tipo alfa
        $pos107_108 = Cnab240Formatter::numerico($this->numEspecie, 2); # Espécie do titulo, geralmente vai ser DM, entretanto, vou deixar o controller/gerador decidir o que fazer com o titulo; tipo numerico
        $pos109_109 = Cnab240Formatter::alfa('A', 1); # Identificação de titulo aceito/nao aceito; tipo alfa
        $pos110_117 = Cnab240Formatter::data($this->boleto->data_registro); # Data de emissão do titulo; tipo numerico
        $pos118_118 = Cnab240Formatter::numerico($this->boleto->codigo_juros, 1); # Codigo de juros MORA, 0 isento, 1 valor por dia, 2 taxa mensal; tipo numerico
        $pos119_126 = Cnab240Formatter::data(Carbon::parse($this->boleto->parcela->data_vencimento)->addDay()); # Data de inicio de cobrança de juro, (1 dia após o vencimento); tipo numerico
        $pos127_141 = Cnab240Formatter::valor($this->boleto->valor_juros, 13); # Valor de juros; tipo numerico
        $pos142_142 = Cnab240Formatter::numerico('0', 1); # Caso o beneficiário seja optante por desconto, 0 é não conceder desconto; tipo numerico
        $pos143_150 = Cnab240Formatter::numerico(0, 8); # Data limite do desconto, documentação não fala o que colocar caso seja optante por não desconto; tipo numerico
        $pos151_165 = Cnab240Formatter::valor('0', 13); # Valor do desconto; tipo numerico
        $pos166_180 = Cnab240Formatter::numerico('0', 13); # Valor do IOF a ser recolhido, zerado por documentação; tipo numerico
        $pos181_195 = Cnab240Formatter::numerico('0', 13); # Valor do abatimento, zerado conforme alinhamento interno; tipo numerico
        $pos196_220 = Cnab240Formatter::alfa($this->boleto->numero_documento, 25); # Identificação do titulo na empresa, campo destinado para o uso do beneficiário identificar o titulo; tipo alfa
        $pos221_221 = Cnab240Formatter::numerico($this->boleto->codigo_protesto, 1); # Codigo de protesto, 1 protestar dias corridos, 2 valor dias uteis, 3 não protestar, 8 negativação sem protesto, 9 negativação automática, responsabilidade do usuario decidir; tipo numerico
        $pos222_223 = Cnab240Formatter::numerico($this->boleto->codigo_protesto !== '3' && $this->boleto->prazo_protesto ? $this->boleto->prazo_protesto : 0, 2); # prazo em dias para protesto; tipo numerico
        $pos224_224 = Cnab240Formatter::numerico("0", 1); # codigo para baixa/devolução sempre 0 conforme documentação; tipo numerico
        $pos225_227 = Cnab240Formatter::alfa("", 3); # Numero de dias para baixa/devolução, preencher com ESPAÇOS EM BRANCO conforme documentação; tipo alfa
        $pos228_229 = Cnab240Formatter::numerico("09", 2); # moeda, 09 = real; tipo numerico
        $pos230_239 = Cnab240Formatter::numerico("0000000000", 10); # Numero do contrato da operação de crédito, sempre 0 conforme documentação; tipo numerico
        $pos240_240 = Cnab240Formatter::alfa("", 1); # Uso exclusivo ferbraban; tipo alfa

        $linha =
            $pos01_03 .
            $pos04_07 .
            $pos08_08 .
            $pos09_13 .
            $pos14_14 .
            $pos15_15 .
            $pos16_17 .
            $pos18_22 .
            $pos23_23 .
            $pos24_35 .
            $pos36_36 .
            $pos37_37 .
            $pos38_57 .
            $pos58_58 .
            $pos59_59 .
            $pos60_60 .
            $pos61_61 .
            $pos62_62 .
            $pos63_77 .
            $pos78_85 .
            $pos86_100 .
            $pos101_105 .
            $pos106_106 .
            $pos107_108 .
            $pos109_109 .
            $pos110_117 .
            $pos118_118 .
            $pos119_126 .
            $pos127_141 .
            $pos142_142 .
            $pos143_150 .
            $pos151_165 .
            $pos166_180 .
            $pos181_195 .
            $pos196_220 .
            $pos221_221 .
            $pos222_223 .
            $pos224_224 .
            $pos225_227 .
            $pos228_229 .
            $pos230_239 .
            $pos240_240;
            
        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Segmento P inválido. Tamanho: ' . strlen($linha)
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