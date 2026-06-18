<?php

namespace App\Services;

use App\Models\ConfiguracaoCobranca;

class ConfiguracaoCobrancaService
{
    public function store(array $dados): ConfiguracaoCobranca
    {
        return ConfiguracaoCobranca::create($dados);
    }

    public function updateOrCreate(array $dados, $contaId){
        return ConfiguracaoCobranca::updateOrCreate(
            ['conta_id' => $contaId],
            [
                'empresa_parametro_id' => $dados['empresa_parametro_id'],
                'integracao_id' => $dados['integracao_id'] ?? null,
                'codigo_cedente' => $dados['codigo_cedente'],
                'codigo_juros' => $dados['codigo_juros'] ?? null,
                'valor_juros' => $dados['valor_juros'] ?? null,
                'dias_inicio_juros' => $dados['dias_inicio_juros'] ?? null,
                'codigo_multa' => $dados['codigo_multa'] ?? null,
                'valor_multa' => $dados['valor_multa'] ?? null,
                'dias_inicio_multa' => $dados['dias_inicio_multa'] ?? null,
                'dias_limite_pagamento' => $dados['dias_limite_pagamento'] ?? null,
                'carteira' => $dados['carteira'],
                'layout_cnab' => $dados['layout_cnab'],
                'ambiente' => $dados['ambiente'],
                'numero_inicial_cobranca' => $dados['numero_inicial_cobranca'],
            ]
        );
    }
    public function show(): ?ConfiguracaoCobranca
    {
        return ConfiguracaoCobranca::first();
    }
}
