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
                'codigo_cedente' => $dados['codigo_cedente'],
                'carteira' => $dados['carteira'],
                'layout_cnab' => $dados['layout_cnab'],
                'ambiente' => $dados['ambiente'],
                'ultimo_numero_remessa' => $dados['ultimo_numero_remessa'],
                'nosso_numero' => $dados['nosso_numero']
            ]
        );
    }
    public function show(): ?ConfiguracaoCobranca
    {
        return ConfiguracaoCobranca::first();
    }
}
