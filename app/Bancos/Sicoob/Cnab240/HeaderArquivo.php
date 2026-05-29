<?php
namespace App\Bancos\Sicoob\Cnab240;

use App\Models\Conta;

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
        $pos72_72 = Cnab240Formatter::alfa(0, 1); # Dígito Verificador da Ag/Conta: Preencher com zeros (conforme documentação); tipo alfa
        
    }

    private function separarDv($valor): array{
        list($num, $dv) = explode('-', $valor);

        return [
            'num' => $num,
            'dv' => $dv
        ];
    }
}