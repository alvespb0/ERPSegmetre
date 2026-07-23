<?php

namespace App\Bancos\Sicoob\Payloads;

use App\Models\Conta;

class PagamentoPayload
{
    public function payloadMount(array $boletoPagamento, Conta $conta): array{
        return array_merge(
            $this->mountBoletoPagamento($boletoPagamento, $conta),
        );
    }

    private function mountBoletoPagamento(array $boletoPagamento, Conta $conta): array {
        return [
            'identificadorConsulta' => $boletoPagamento['identificador_consulta'],
            'valorBoleto' => (double) $boletoPagamento['valor_boleto'],
            'valorDescontoAbatimento' => (double) $boletoPagamento['valor_abatimento'],
            'valorMultaMora' => (double) $boletoPagamento['valor_multa'],
            'aceitaValorDivergente' => (bool) $boletoPagamento['aceita_valor_divergente'],
            'numeroCpfCnpjPortador' => $boletoPagamento['cpf_cnpj_pagador'],
            'nomePortador' => $boletoPagamento['razao_social_pagador'],
            'amount' => (double) $boletoPagamento['valor_final'],
            'debtorAccount' => $this->mountDebtorAccount($conta)
        ];
    }

    private function mountDebtorAccount(Conta $conta){
        return [
            'issuer' => (int) preg_replace('/-/', '', $conta->agencia),
            'number' => (int) preg_replace('/-/', '', $conta->conta),
            'accountType' => 0,
            'personType' => $conta->modalidade == 'pj' ? 1 : 0
        ];
    }

}