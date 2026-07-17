<?php
namespace App\Bancos\Sicoob\Payloads;

use App\Models\BoletoCobranca;
use Carbon\Carbon;

class DDAPayload{
    public function payloadMount($dataInicial, $dataFinal, $numeroConta, $situacao): array{
        return [
            'dataInicial' => $dataInicial,
            'dataFinal' => $dataFinal,
            'situacao' => (int) $situacao,
            'tipoData' => 1, # hardcodado para filtrar por data de vencimento, facilita multi empresa
            'numeroConta' => $numeroConta
        ];
    }
}

?>