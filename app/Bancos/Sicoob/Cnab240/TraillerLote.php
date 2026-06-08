<?php
namespace App\Bancos\Sicoob\Cnab240;

use App\Models\Conta;

class TraillerLote
{
    public Conta $conta;
    public $numeroLote;
    public $qtdRegistrosLote;
    public $qtdCobrancaSimples;
    public $qtdCobrancaVinculada;
    public $qtdCobrancaCaucionada;
    public $qtdCobrancaDescontada;
    public $valorCobrancaSimples;
    public $valorCobrancaVinculada;
    public $valorCobrancaCaucionada;
    public $valorCobrancaDescontada;


    public function __construct(Conta $conta, $numeroLote, $qtdRegistrosLote, $qtdCobrancaSimples = 0, $qtdCobrancaVinculada = 0, $qtdCobrancaCaucionada = 0, $qtdCobrancaDescontada = 0,
                                $valorCobrancaSimples = 0, $valorCobrancaVinculada = 0, $valorCobrancaCaucionada = 0, $valorCobrancaDescontada = 0){
        $this->conta = $conta;
        $this->numeroLote = $numeroLote;
        $this->qtdRegistrosLote = $qtdRegistrosLote;
        $this->qtdCobrancaSimples = $qtdCobrancaSimples;
        $this->qtdCobrancaVinculada = $qtdCobrancaVinculada;
        $this->qtdCobrancaCaucionada = $qtdCobrancaCaucionada;
        $this->qtdCobrancaDescontada = $qtdCobrancaDescontada;
        $this->valorCobrancaSimples = $valorCobrancaSimples;
        $this->valorCobrancaVinculada = $valorCobrancaVinculada;
        $this->valorCobrancaCaucionada = $valorCobrancaCaucionada;
        $this->valorCobrancaDescontada = $valorCobrancaDescontada;
    }

    public function montar(): string {

        $pos01_03 = Cnab240Formatter::numerico($this->conta->banco->numero_banco, 3); # Codigo do banco; tipo numerico;
        $pos04_07 = Cnab240Formatter::numerico($this->numeroLote, 4); # Lote do serviço, o mesmo só é sequencial dentro do mesmo arquivo (tem que ser o mesmo do headerLote); tipo numerico
        $pos08_08 = Cnab240Formatter::numerico("5", 1); # Tipo de registro, hardcodado, sempre será 5; tipo numerico
        $pos09_17 = Cnab240Formatter::alfa("", 9); # Uso exclusivo FEBRABAN, preencher em branco; tipo alfa
        $pos18_23 = Cnab240Formatter::numerico($this->qtdRegistrosLote, 6); # Quantidade de registros no lote; tipo numerico
        $pos24_29 = Cnab240Formatter::numerico($this->qtdCobrancaSimples, 6); # Quantidade de titulos em cobranca simples no lote; tipo numerico
        $pos30_46 = Cnab240Formatter::valor($this->valorCobrancaSimples, 17); # Valor de titulos em cobranca simples no lote; tipo numerico
        $pos47_52 = Cnab240Formatter::numerico($this->qtdCobrancaVinculada, 6); # Quantidade de titulos em cobranca vinculada no lote; tipo numerico
        $pos53_69 = Cnab240Formatter::valor($this->valorCobrancaVinculada, 17); # Valor de titulos em cobranca vinculada no lote; tipo numerico
        $pos70_75 = Cnab240Formatter::numerico($this->qtdCobrancaCaucionada, 6); # Quantidade de titulos em cobranca caucionada no lote; tipo numerico
        $pos76_92 = Cnab240Formatter::valor($this->valorCobrancaCaucionada, 17); # Valor de titulos em cobranca caucionada no lote; tipo numerico
        $pos93_98 = Cnab240Formatter::numerico($this->qtdCobrancaDescontada, 6); # Quantidade de titulos em cobranca descontada no lote; tipo numerico
        $pos99_115 = Cnab240Formatter::valor($this->valorCobrancaDescontada, 17); # Valor de titulos em cobranca descontada no lote; tipo numerico
        $pos116_123 = Cnab240Formatter::alfa("", 8); # Numero do aviso de lançamento, preencher com espaços em branco; tipo alfa
        $pos124_240 = Cnab240Formatter::alfa("", 117); # Uso exclusivo FEBRABAN, preencher com espaços em branco; tipo alfa

        $linha =
            $pos01_03 .
            $pos04_07 .
            $pos08_08 .
            $pos09_17 .
            $pos18_23 .
            $pos24_29 .
            $pos30_46 .
            $pos47_52 .
            $pos53_69 .
            $pos70_75 .
            $pos76_92 .
            $pos93_98 .
            $pos99_115 .
            $pos116_123 .
            $pos124_240;


        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Trailler Lote inválido. Tamanho: ' . strlen($linha)
            );
        }

        \Log::debug('Trailler Lote cnab', ['length' => strlen($linha), 'linha' => $linha]);

        return $linha;
    }

    
}