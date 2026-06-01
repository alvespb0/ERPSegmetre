<?php
namespace App\Bancos\Sicoob\Cnab240;

use App\Models\Conta;
use App\Models\ArquivoRemessa;
use Carbon\Carbon;

class HeaderArquivo
{
    public $conta;
    public $numeroRemessa;
    public $configuracao;

    public function __construct(Conta $conta, $numeroRemessa){
        $this->conta = $conta;
        $this->numeroRemessa = $numeroRemessa;
        $this->configuracao = $conta->configuracaoCobranca;
    }

    public function montar(): string {
        $agencia = $this->separarDv($this->conta->agencia); # array
        $contaBancaria = $this->separarDv($this->conta->conta); # array
        
        $pos01_03 = Cnab240Formatter::numerico($this->conta->banco->numero_banco, 3); # Codigo do banco; tipo numerico;
        $pos04_07 = Cnab240Formatter::numerico("0000", 4); # Lote de serviço; tipo numerico; hard coded, sempre é 0000; 
        $pos08_08 = Cnab240Formatter::numerico("0", 1); # Tipo de registro; tipo numerico; hard coded; sempre é 0;
        $pos09_17 = Cnab240Formatter::alfa("", 9); # Uso exclusivo da FEBRABAN; tipo alfa; 
        $pos18_18 = Cnab240Formatter::numerico(strlen($this->configuracao->empresaParametro->cnpj) > 11 ? "2" : "1", 1); # Tipo de inscrição da empresa 1 CPF 2 CNPJ; tipo numerico
        $pos19_32 = Cnab240Formatter::numerico($this->configuracao->empresaParametro->cnpj, 14); # Numero de inscrição, no BD é sempre salvo de forma numérica sem traços então sem necessidade de formatar; tipo numerico
        $pos33_52 = Cnab240Formatter::alfa("", 20); # Convenio preencher em espaços em branco; tipo alfa
        $pos53_57 = Cnab240Formatter::numerico($agencia['num'], 5); # Número da agencia; tipo numerico
        $pos58_58 = Cnab240Formatter::alfa($agencia['dv'], 1); # Dígito verificador da agencia; tipo alfa
        $pos59_70 = Cnab240Formatter::numerico($contaBancaria['num'], 12); # Número da conta bancaria; tipo numerico
        $pos71_71 = Cnab240Formatter::numerico($contaBancaria['dv'], 1); # digito verificador da conta bancaria; tipo numerico
        $pos72_72 = Cnab240Formatter::alfa("0", 1); # Dígito Verificador da Ag/Conta: Preencher com zeros (conforme documentação); tipo alfa
        $pos73_102 = Cnab240Formatter::alfa($this->configuracao->empresaParametro->razao_social, 30); # Nome da empresa, conforme documentação, do beneficiário; tipo alfa
        $pos103_132 = Cnab240Formatter::alfa("SICOOB", 30); # Hardcodado, sempre será sicoob conforme documentação; tipo alfa
        $pos133_142 = Cnab240Formatter::alfa("", 10); # Uso exclusivo FEBRABAN, preencher com espaços em brancos; tipo alfa
        $pos143_143 = Cnab240Formatter::numerico("1", 1); # Código da remessa, hardcodado 1 conforme documentação; tipo numerico
        $pos144_151 = Cnab240Formatter::data(Carbon::today()); # Dia de geração da remessa, o formato é dmy, o formatter cuidara disso com parse; tipo numerico
        $pos152_157 = Cnab240Formatter::hora(now()); # Hora da geração da remessa, o formato é Hsi, o formatter cuidara disso; tipo numerico
        $pos158_163 = Cnab240Formatter::numerico($this->numeroRemessa, 6); # NSA (numero sequencial da remessa); tipo numerico
        $pos164_166 = Cnab240Formatter::numerico("081", 3); # Layout do arquivo, hardcodado conforme documentação; numerico
        $pos167_171 = Cnab240Formatter::numerico("00000", 5); # Densidade de gravação do arquivo, 00000 hardcoado conforme documentação; numerico
        $pos172_191 = Cnab240Formatter::alfa("", 20); # Uso reservado do banco; tipo alfa
        $pos192_211 = Cnab240Formatter::alfa("", 20); # Uso reservado da empresa, preencher em branco; tipo alfa
        $pos212_240 = Cnab240Formatter::alfa("", 29); # Uso reservado da FEBRABAN preencher em branco; tipo alfa

        $linha = 
            $pos01_03 .
            $pos04_07 .
            $pos08_08 .
            $pos09_17 .
            $pos18_18 .
            $pos19_32 .
            $pos33_52 .
            $pos53_57 .
            $pos58_58 .
            $pos59_70 .
            $pos71_71 .
            $pos72_72 .
            $pos73_102 .
            $pos103_132 .
            $pos133_142 .
            $pos143_143 .
            $pos144_151 .
            $pos152_157 .
            $pos158_163 .
            $pos164_166 .
            $pos167_171 .
            $pos172_191 .
            $pos192_211 .
            $pos212_240;

        if (strlen($linha) !== 240) {
            throw new \Exception(
                'HeaderArquivo inválido. Tamanho: ' . strlen($linha)
            );
        }
        \Log::debug('Debug de header cnab', ['length' => strlen($linha), 'linha' => $linha]);
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