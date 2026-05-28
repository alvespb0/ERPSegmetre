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

    }
}