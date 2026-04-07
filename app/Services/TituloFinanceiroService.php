<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\TituloFinanceiro;
use App\Models\Parcela;
use Illuminate\Support\Facades\DB;

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
            'tipo' => $dados['tipo'],
            'status' => $dados['status'],
        ]);
    }

    public function update(array $dados, $id){
        $titulo = TituloFinanceiro::findOrFail($id);

        return $titulo->update($dados);
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

    public function alterarStatusTitulo(Parcela $parcela, $novoStatus): array{
        if($novoStatus == 'cancelado'){
            return $this->cancelarTitulo($parcela);
        }

        if($novoStatus == 'ativo'){
            $titulo = $parcela->titulo;
            DB::transaction(function () use ($titulo) {
                $this->update([
                    'status' => 'ativo'
                ], $titulo->id); 
                
                $titulo->parcelas()->update([
                    'status' => 'ativo'
                ]);
            });
            
            return ['status' => true, 'message' => 'Status alterado com sucesso'];
        }

        if($novoStatus == 'suspenso' || $novoStatus == 'renegociado'){
            $titulo = $parcela->titulo;
            DB::transaction(function () use ($titulo) {
                $this->update([
                    'status' => $novoStatus,
                ], $titulo->id); 

                $titulo->parcelas()
                    ->doesntHave('movimentacoes')
                    ->update([
                        'status' => $novoStatus
                    ]);
            });

            return ['status' => true, 'message' => 'Status alterado para o título e todas as parcelas sem movimentação.'];
        }

        return ['status' => false, 'message' => 'Status inválido.'];
    }

    private function cancelarTitulo(Parcela $parcela): array{
        if($parcela->titulo->saldo_devedor != $parcela->titulo->valor_total){ # só dá para marcar como cancelado na condição de que titulo nao tenha movimentações geradas
            return ['status' => false, 'message' => 'Não foi possível cancelar o título, o mesmo já possui movimentações realizadas.'];
        }

        $titulo = $parcela->titulo;
        DB::transaction(function () use ($titulo) {
            $this->update([
                'status' => 'cancelado'
            ], $titulo->id); 
            
            $titulo->parcelas()->update([
                'status' => 'cancelado'
            ]);
        });

        return ['status' => true, 'message' => 'Titulo e parcelas cancelados com sucesso.'];
    }

}