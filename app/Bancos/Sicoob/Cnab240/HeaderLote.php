<?php
namespace App\Bancos\Sicoob\Cnab240;

use App\Models\Conta;
use App\Models\ArquivoRemessa;
use Carbon\Carbon;

class HeaderLote
{
    public $conta;
    public $numeroRemessa;
    public $configuracao;

    public function __construct(Conta $conta, $numeroLote, $numeroRemessa){
        $this->conta = $conta;
        $this->numeroLote = $numeroLote;
        $this->numeroRemessa = $numeroRemessa;
        $this->configuracao = $conta->configuracaoCobranca;
    }

    public function montar(): string {
        $agencia = $this->separarDv($this->conta->agencia); # array
        $contaBancaria = $this->separarDv($this->conta->conta); # array

        $pos01_03 = Cnab240Formatter::numerico($this->conta->banco->numero_banco, 3); # Codigo do banco; tipo numerico;
        $pos04_07 = Cnab240Formatter::numerico($this->numeroLote, 4); # Lote do serviço, o mesmo só é sequencial dentro do mesmo arquivo; tipo numerico
        $pos08_08 = Cnab240Formatter::numerico("1", 1); # Tipo de registro, hardcodado, sempre será 1; tipo numerico
        $pos09_09 = Cnab240Formatter::alfa("R", 1); # Tipo de operação, hardcodado, sempre será R; tipo alfa
        $pos10_11 = Cnab240Formatter::numerico("01", 2); # Tipo de serviço, hardcodado, sempre será 01; tipo numerico
        $pos12_13 = Cnab240Formatter::alfa("", 2); # Uso exclusivo da FEBRABAN; tipo alfa
        $pos14_16 = Cnab240Formatter::numerico("040", 3); # número da versão do layout do lote; tipo numerico
        $pos17_17 = Cnab240Formatter::alfa("", 1); # Uso exclusivo da FEBRABAN; tipo alfa
        $pos18_18 = Cnab240Formatter::numerico(strlen($this->configuracao->empresaParametro->cnpj) > 11 ? "2" : "1", 1); # Tipo de inscrição da empresa 1 CPF 2 CNPJ; tipo numerico
        $pos19_33 = Cnab240Formatter::numerico($this->configuracao->empresaParametro->cnpj, 15); # Numero de inscrição, no BD é sempre salvo de forma numérica sem traços então sem necessidade de formatar; tipo numerico
        $pos34_53 = Cnab240Formatter::alfa("", 20); # Convenio do banco, preencher em branco; tipo alfa
        $pos54_58 = Cnab240Formatter::numerico($agencia['num'], 5); # Número da agencia; tipo numerico
        $pos59_59 = Cnab240Formatter::alfa($agencia['dv'], 1); # Dígito verificador da agencia; tipo alfa
        $pos60_71 = Cnab240Formatter::numerico($contaBancaria['num'], 12); # Número da conta bancaria; tipo numerico
        $pos72_72 = Cnab240Formatter::numerico($contaBancaria['dv'], 1); # digito verificador da conta bancaria; tipo numerico
        $pos73_73 = Cnab240Formatter::alfa("", 1); # Digito verificador da ag/conta: preencher com espaços em branco; tipo alfa
        $pos74_103 = Cnab240Formatter::alfa($this->configuracao->empresaParametro->razao_social, 30); # nome da empresa; tipo alfa
        $pos104_143 = Cnab240Formatter::alfa("", 40); # Mensagem 1. Preencher com espaços em branco (conforme documentacao); tipo alfa
        $pos144_183 = Cnab240Formatter::alfa("", 40); # Mensagem 2. Preencher com espaços em branco (conforme documentacao); tipo alfa
        $pos184_191 = Cnab240Formatter::numerico($this->numeroRemessa, 8); # NSA o controller vai passar no constructor o numero de remessa; tipo numerico
        $pos192_199 = Cnab240Formatter::data(Carbon::today()); # Data de gravação da remessa; tipo numerico
        $pos200_207 = Cnab240Formatter::numerico("0", 8); # Data do crédito 00000000; tipo numerico
        $pos208_240 = Cnab240Formatter::alfa("", 33); # Uso exclusivo FEBRABAN; tipo alfa

        $linha =
            $pos01_03 .
            $pos04_07 .
            $pos08_08 .
            $pos09_09 .
            $pos10_11 .
            $pos12_13 .
            $pos14_16 .
            $pos17_17 .
            $pos18_18 .
            $pos19_33 .
            $pos34_53 .
            $pos54_58 .
            $pos59_59 .
            $pos60_71 .
            $pos72_72 .
            $pos73_73 .
            $pos74_103 .
            $pos104_143 .
            $pos144_183 .
            $pos184_191 .
            $pos192_199 .
            $pos200_207 .
            $pos208_240;

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