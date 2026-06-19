<?php

namespace App\Services;

use App\Models\BoletoCobranca;

class BoletoCobrancaService
{
    public function store(array $dados): BoletoCobranca
    {
        return BoletoCobranca::create($this->mapearDados($dados));
    }

    public function update(array $dados, int $id): bool
    {
        $boleto = BoletoCobranca::findOrFail($id);

        return $boleto->update($this->mapearDados($dados));
    }

    public function find(int $id): BoletoCobranca
    {
        return BoletoCobranca::with($this->relacionamentos())
            ->findOrFail($id);
    }

    public function show()
    {
        return BoletoCobranca::with($this->relacionamentos())
            ->orderByDesc('created_at')
            ->get();
    }

    public function destroy(int $id): bool
    {
        return BoletoCobranca::findOrFail($id)->delete();
    }

    public function showTrashed()
    {
        return BoletoCobranca::onlyTrashed()
            ->with($this->relacionamentos())
            ->orderByDesc('created_at')
            ->get();
    }

    public function restore(int $id): bool
    {
        return BoletoCobranca::withTrashed()->findOrFail($id)->restore();
    }

    private function mapearDados(array $dados): array
    {
        return [
            'parcela_id' => $dados['parcela_id'],
            'configuracao_cobranca_id' => $dados['configuracao_cobranca_id'] ?? null,
            'arquivo_remessa_id' => $dados['arquivo_remessa_id'] ?? null,
            'arquivo_retorno_id' => $dados['arquivo_retorno_id'] ?? null,
            'nosso_numero' => $dados['nosso_numero'] ?? null,
            'sequencial_boleto' => $dados['sequencial_boleto'],
            'pdf_path' => $dados['pdf_path'] ?? null,
            'numero_documento' => $dados['numero_documento'],
            'modalidade' => $dados['modalidade'],
            'info_complementares' => $dados['info_complementares'] ?? null,
            'especie_documento' => $dados['especie_documento'],
            'linha_digitavel' => $dados['linha_digitavel'] ?? null,
            'codigo_barras' => $dados['codigo_barras'] ?? null,
            'status' => $dados['status'] ?? 'pendente',
            'codigo_multa' => $dados['codigo_multa'],
            'codigo_juros' => $dados['codigo_juros'] ?? '0',
            'codigo_protesto' => $dados['codigo_protesto'] ?? '3',
            'valor_multa' => $dados['valor_multa'] ?? 0,
            'valor_juros' => $dados['valor_juros'] ?? 0,
            'data_registro' => $dados['data_registro'] ?? null,
            'data_multa' => $dados['data_multa'] ?? null,
            'data_juro' => $dados['data_juro'] ?? null,
            'data_liquidacao' => $dados['data_liquidacao'] ?? null,
            'prazo_protesto' => $dados['prazo_protesto'] ?? null,
        ];
    }

    private function relacionamentos(): array
    {
        return [
            'parcela',
            'configuracaoCobranca',
            'arquivoRemessa',
            'arquivoRetorno',
        ];
    }
}
