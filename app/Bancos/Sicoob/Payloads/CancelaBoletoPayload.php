<?php

namespace App\Bancos\Sicoob\Payloads;

use App\Models\BoletoCobranca;

class CancelaBoletoPayload
{
    public function payloadMount(BoletoCobranca $boleto): array{
        return [
            'numeroCliente' => (int) $boleto
                ->configuracaoCobranca
                ->codigo_cedente,

            'codigoModalidade' => (int) $boleto
                ->modalidade,
        ];
    }
}