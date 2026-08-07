<?php

namespace App\Bancos\Sicoob\Payloads;

class IniciaPixPayload
{
    public function payloadMount($chave, $dataPagamento = null): array{
        $data = [];

        if ($dataPagamento && $dataPagamento->gt(Carbon::today())) {
            $data['dataAgendamento'] = $dataPagamento;
        }

        return array_merge([
            'chave' => $chave,
        ], $data);
    }
}