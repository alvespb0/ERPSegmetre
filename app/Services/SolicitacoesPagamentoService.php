<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\SolicitacoesPagamento;

class SolicitacoesPagamentoService
{
    public function store(array $dados){
        return SolicitacoesPagamento::create([
            'parcela_id' => $dados['parcela_id'] ?? null,
            'movimentacao_id' => $dados['movimentacao_id'] ?? null,
            'conta_id' => $dados['conta_id'] ?? null,
            'chave_idempotente' => $dados['chave_idempotente'] ?? null,
            'tipo' => $dados['tipo'],
            'identificador' => $dados['identificador'],
            'valor' => $dados['valor'],
            'data_solicitacao' => $dados['data_solicitacao'] ?? \Carbon\Carbon::now(),
            'data_pagamento' => $dados['data_pagamento'] ?? null,
            'comprovante_path' => $dados['comprovante_path'] ?? null,
            'codigo_autenticador' => $dados['codigo_autenticador'] ?? null,
            'end_to_end_id' => $dados['end_to_end_id'] ?? null,
            'id_pagamento' => $dados['id_pagamento'] ?? null, 
            'status' => $dados['status'] ?? 'pendente'
        ]);
    }

    public function update($solicitacaoId, array $dados){
        $solicitacao = SolicitacoesPagamento::findOrFail($solicitacaoId);
        return $solicitacao->update($dados);
    }

    public function makeComprovantePagamento(array $dados){
        $fileName = 'comprovante-' . time() . '.pdf';

        $pdf = Pdf::loadView('erp.pdf.comprovante-pagamento', ['retorno' => $dados]);

        $path = "comprovantes/{$fileName}";

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}

?>