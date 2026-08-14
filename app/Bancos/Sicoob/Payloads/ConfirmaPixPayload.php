<?php

namespace App\Bancos\Sicoob\Payloads;

class ConfirmaPixPayload
{
    public function payloadMount($e2eId, $valor, $meioIniciacao): array{
        $valor = number_format((float) $valor, 2, ',', '');
        
        return array_merge([
            'endToEndId' => $e2eId,
            'meioIniciacao' => $meioIniciacao,
            'valor' => $valor,
            'repeticao' => true,
        ]);
    }
}