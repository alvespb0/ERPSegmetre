<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\TituloFinanceiro;

class TituloFinanceiroService
{
    public function store(array $dados){
        return TituloFinanceiro::create([
            'centro_custo_id' => $dados['centro_custo_id'] ?? null,
            'categoria_financeira_id' => $dados['categoria_financeira_id'] ?? null,
            'conta_id' => $dados['conta_id'] ?? null,
            'entidade_id' => $dados['entidade_id'],
            'descricao' => $dados['descricao'],
            'observacoes' => $dados['observacoes'] ?? null,
            'numero_nf' => $dados['numero_nf'] ?? null,
            'valor_total' => $dados['valor_total'],
            'data_emissao' => $dados['data_emissao'],
            'data_vencimento' => $dados['data_vencimento'],
            'tipo' => $dados['tipo'],
            'status' => $dados['status'],
        ]);
    }

    public function update(array $dados, $id){
        $titulo = TituloFinanceiro::findOrFail($id);

        return $titulo->update([
            'centro_custo_id' => $dados['centro_custo_id'] ?? null,
            'categoria_financeira_id' => $dados['categoria_financeira_id'] ?? null,
            'conta_id' => $dados['conta_id'] ?? null,
            'entidade_id' => $dados['entidade_id'],
            'descricao' => $dados['descricao'],
            'observacoes' => $dados['observacoes'] ?? null,
            'numero_nf' => $dados['numero_nf'] ?? null,
            'valor_total' => $dados['valor_total'],
            'data_emissao' => $dados['data_emissao'],
            'data_vencimento' => $dados['data_vencimento'],
            'tipo' => $dados['tipo'],
            'status' => $dados['status'],
        ]);
    }

    public function show(){
        return TituloFinanceiro::orderBy('data_vencimento', 'asc')->get();
    }

    public function destroy($id){
        $titulo = TituloFinanceiro::findOrFail($id);

        return $titulo->delete();
    }

    public function showTrashed(){
        return TituloFinanceiro::orderBy('data_vencimento', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return TituloFinanceiro::withTrashed()->find($id)->restore();
    }
}