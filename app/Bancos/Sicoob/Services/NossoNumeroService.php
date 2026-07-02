<?php

namespace App\Bancos\Sicoob\Services;

use App\Models\BoletoCobranca;

class NossoNumeroService
{
    protected BoletoCobranca $boleto;

    public function __construct(BoletoCobranca $boleto)
    {
        $this->boleto = $boleto;
    }

    /**
     * Gera o NumTitulo conforme item 3.13.
     *
     * Retorna:
     * 0000000218
     *
     * (Nosso Número sequencial + DV)
     */
    public function gerarNumTitulo(): string
    {
        $configuracao = $this->boleto->configuracaoCobranca;

        $arrayCoperativa = $this->separarDv($configuracao->conta->agencia); # array num + dv
        $cooperativa = str_pad($arrayCoperativa['num'], 4, '0', STR_PAD_LEFT); # SÓ O NÚMERO SEM O DV

        $codigoCliente = str_pad(preg_replace('/\D/', '', $configuracao->codigo_cedente), 10, '0', STR_PAD_LEFT);

        $sequencial = str_pad($this->boleto->sequencial_boleto, 7, '0', STR_PAD_LEFT);

        $base = $cooperativa . $codigoCliente . $sequencial;

        $constante = '319731973197319731973';

        $soma = 0;

        /* Alinhar a constante com a sequencia repetindo de traz pra frente */
        for ($i = 0; $i < strlen($base); $i++) {
            $soma += ((int) $base[$i]) * ((int) $constante[$i]); /* multiplicar cada componente da sequencia com o seu correspondente da constante e somar os resultados */
        }

        $resto = $soma % 11; /* calcular o resto modulo 11 */

        $dv = ($resto <= 1)
            ? 0
            : (11 - $resto);

        /*
         * Item 3.13
         * Nosso Número (7)
         * +
         * DV (1)
         * = 8 posições
         */
        $numTitulo = $sequencial . $dv;

        /*
         * Campo do CNAB possui 10 posições.
         */
        return str_pad($numTitulo, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Monta o campo Nosso Número de 20 posições
     * utilizado no Segmento P.
     */
    public function gerarNossoNumero(): string
    {
        $numTitulo = $this->gerarNumTitulo();

        $parcela = str_pad($this->boleto->parcela->numero_parcela, 2,'0', STR_PAD_LEFT);

        $modalidade = str_pad($this->boleto->modalidade,2,'0',STR_PAD_LEFT);

        $tipoFormulario = '4'; # 4 = A4 sem envelopamento

        return $numTitulo . $parcela . $modalidade . $tipoFormulario . str_repeat(' ', 5);
    }

    private function separarDv($valor): array{
        list($num, $dv) = explode('-', $valor);

        return [
            'num' => $num,
            'dv' => $dv
        ];
    }

}