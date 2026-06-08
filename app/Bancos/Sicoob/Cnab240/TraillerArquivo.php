<?php
namespace App\Bancos\Sicoob\Cnab240;

use App\Models\Conta;

class TraillerArquivo
{
    public Conta $conta;
    public $numeroLote;
    public $qtdLotesArquivo;
    public $qtdRegistrosArquivo;


    public function __construct(Conta $conta, $qtdLotesArquivo, $qtdRegistrosArquivo){
        $this->conta = $conta;
        $this->numeroLote = $numeroLote;
        $this->qtdLotesArquivo = $qtdLotesArquivo;
        $this->qtdRegistrosArquivo = $qtdRegistrosArquivo;
    }

    public function montar(): string {

        $pos01_03 = Cnab240Formatter::numerico($this->conta->banco->numero_banco, 3); # Codigo do banco; tipo numerico;
        $pos04_07 = Cnab240Formatter::numerico("9999", 4); # numero do lote, hardcodado, sempre vai ser 9999; tipo numerico
        $pos08_08 = Cnab240Formatter::numerico("9", 1); # Tipo de registro, hardcodado, sempre será 5; tipo numerico
        $pos09_17 = Cnab240Formatter::alfa("", 9); # Uso exclusivo FEBRABAN, preencher com espaços em branco; tipo alfa
        $pos18_23 = Cnab240Formatter::numerico($this->qtdLotesArquivo, 6); # Quantidade de lotes do arquivo; tipo numerico
        $pos24_29 = Cnab240Formatter::numerico($this->qtdRegistrosArquivo, 6); # Quantidade de registros do arquivo; tipo numerico
        $pos30_35 = Cnab240Formatter::numerico("000000", 6); # Quantidade de contas, hardcodado conforme documentação, sempre 000000; tipo numerico
        $pos26_240 = Cnab240Formatter::alfa("", 205); # Uso exclusivo Febraban, tipo alfa;

        $linha = 
            $pos01_03 .
            $pos04_07 .
            $pos08_08 .
            $pos09_17 .
            $pos18_23 .
            $pos24_29 .
            $pos30_35 .
            $pos26_240;

        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Trailler Arquivo inválido. Tamanho: ' . strlen($linha)
            );
        }

        \Log::debug('Trailler Arquivo cnab', ['length' => strlen($linha), 'linha' => $linha]);

        return $linha;
    }

    
}