<?php

namespace App\Bancos\Sicoob\Payloads;

use App\Models\BoletoCobranca;

class ConsultaBoletoPayload
{
    public function payloadMount(BoletoCobranca $boleto): array{
        return array_merge(
            $this->mountParametrosBase($boleto),
            $this->mountIdentificador($boleto)
        );
    }

    private function mountParametrosBase(BoletoCobranca $boleto): array {
        return [
            'numeroCliente' => (int) $boleto
                ->configuracaoCobranca
                ->codigo_cedente,

            'codigoModalidade' => (int) $boleto
                ->modalidade,
        ];
    }

    private function mountIdentificador(BoletoCobranca $boleto): array {

        if ($boleto->nosso_numero) {
            return [
                'nossoNumero' => $boleto->nosso_numero
            ];
        }

        if ($boleto->linha_digitavel) {
            return [
                'linhaDigitavel' => $boleto->linha_digitavel
            ];
        }

        if ($boleto->codigo_barras) {
            return [
                'codigoBarras' => $boleto->codigo_barras
            ];
        }

        throw new \Exception(
            'Nenhum identificador válido encontrado.'
        );
    }
}