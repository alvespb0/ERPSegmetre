<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\SolicitacoesPagamento;

class SolicitacoesPagamentoService
{
    public function store(array $dados){
        return SolicitacoesPagamento::create([
            'parcela_id' => $dados['parcela_id'],
            'movimentacao_id' => $dados['movimentacao_id'] ?? null,
            'chave_idempotente' => $dados['chave_idempotente'],
            'tipo' => $dados['tipo'],
            'identificador' => $dados['identificador'],
            'valor' => $dados['valor'],
            'data_solicitacao' => $dados['data_solicitacao'] ?? Carbon\Carbon::today()->toDateString(),
            'data_pagamento' => $dados['data_pagamento'] ?? null,
            'comprovante_path' => $dados['comprovante_path'] ?? null,
            'status' => $dados['status'] ?? 'pendente'
        ]);
    }

    public function update($solicitacaoId, array $dados){
        $solicitacao = SolicitacoesPagamento::findOrFail($solicitacaoId);
        return $solicitacao->update($dados);
    }
}

?>