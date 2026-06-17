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
