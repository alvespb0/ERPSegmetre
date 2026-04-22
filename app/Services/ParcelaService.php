<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParcelaService
{
    public function store(array $dados){
        return Parcela::create([
            'titulo_financeiro_id' => $dados['titulo_financeiro_id'] ?? null,
            'numero_parcela' => $dados['numero_parcela'],
            'valor' => $dados['valor'],
            'data_vencimento' => $dados['data_vencimento'],
            'status' => $dados['status']
        ]);
    }

    public function update(array $dados, $id){
        $parcela = Parcela::findOrFail($id);

        return $parcela->update($dados);
    }

    public function show(){
        return Parcela::orderBy('data_vencimento', 'asc')->get();
    }

    public function destroy($id){
        $parcela = Parcela::findOrFail($id);

        return $parcela->delete();
    }

    public function showTrashed(){
        return Parcela::orderBy('data_vencimento', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return Parcela::withTrashed()->find($id)->restore();
    }

    public function gerarParcelas($valor_total, $quantidade_parcelas, $data_vencimento){
        $parcelas = [];
        $valorTotalCentavos = (int) round($valor_total * 100);
        $valorParcelaCentavos = intdiv($valorTotalCentavos, $quantidade_parcelas);

        $somaCentavos = $valorParcelaCentavos * $quantidade_parcelas;
        $diferencaCentavos = $valorTotalCentavos - $somaCentavos;

        for($i = 0; $i < $quantidade_parcelas; $i++){
            $valorCentavos = $valorParcelaCentavos;

            if ($i === $quantidade_parcelas - 1) {
                $valorCentavos += $diferencaCentavos;
            }

            $data = Carbon::parse($data_vencimento);
            $parcelas[] = [
                'parcela_numero' => $i + 1,
                'data_vencimento_parcela' => $data->addMonths($i)->format('Y-m-d'),
                'valor_parcela' => $valorCentavos/100,
            ];
        }

        return $parcelas;
    }

    public function alterarStatusParcela(Parcela $parcela, $novoStatus, $tipoAjuste = null): array{
        if($novoStatus == 'cancelado'){
            if(!$tipoAjuste){
                return ['status' => false, 'message' => 'Obrigatório o tipo de ajuste na parcela (redistribuição ou desconto).'];
            }

            if($tipoAjuste == 'desconto'){
                return $this->aplicarDesconto($parcela);
            }

            if($tipoAjuste == 'redistribuir'){
                return $this->redistribuirParcela($parcela);
            }
        }

        if($novoStatus == 'suspenso' || $novoStatus == 'renegociado'){
            if($parcela->movimentacoes()->exists()){
                return ['status' => false, 'message' => 'Não foi possível alterar o status da parcela, a mesma possui movimentaçoes.'];
            }

            $this->update(['status' => $novoStatus], $parcela->id);

            return ['status' => true, 'message' => 'Status alterado com sucesso.'];
        }

        if($novoStatus == 'ativo'){                    
            $this->update(['status' => $novoStatus], $parcela->id);

            return ['status' => true, 'message' => 'Status alterado com sucesso.'];
        }

        return ['status' => false, 'message' => 'Status inválido.'];
    }

    private function redistribuirParcela($parcela): array{
        $numParcelasFaltantes = $parcela->titulo->parcelas_faltantes - 1; # -1 por que vai cancelar essa parcela
        if($numParcelasFaltantes <= 0){
            return ['status' => false, 'message' => 'Não há parcelas restantes para distribuição de valor.'];
        }
        $saldoDevedor = $parcela->titulo->saldo_devedor;

        $titulo_id = $parcela->titulo->id;

        $dataVencInicial = $parcela->data_vencimento;

        DB::transaction(function () use ($numParcelasFaltantes, $saldoDevedor, $parcela, $dataVencInicial, $titulo_id) {
            /* Parcela atual vinda do mount passa a ter valor zerado e status cancelado */
            $this->update(['status' => 'cancelado'], $parcela->id);

            /* Pega todas as parcelas restantes desse titulo, transforma em collection com get, e filtra as que não estão com status dinamico pago */
            $parcelasAtuais = Parcela::where('titulo_financeiro_id', $titulo_id)
                ->get()
                ->filter(function ($parcela) {
                    return $parcela->status_calculado != 'pago' && $parcela->status_calculado != 'cancelado';
                });

            /* Gerado novas parcelas para reaproveitar lógica de centavos */
            $novasParcelas = $this->gerarParcelas($saldoDevedor, $numParcelasFaltantes, $dataVencInicial);

            foreach ($parcelasAtuais->values() as $index => $parcelaAtual) {
                if (!isset($novasParcelas[$index])) {
                    continue;
                }
                $this->update([
                    'valor' => $novasParcelas[$index]['valor_parcela'],
                    'status' => 'ativo'
                ], $parcelaAtual->id);
            }
        });
        
        return ['status' => true, 'message' => 'Parcela redistribuída com sucesso.'];
    }

    private function aplicarDesconto(Parcela $parcela): array{
        $tituloService = new TituloFinanceiroService; 
        $titulo = $parcela->titulo;

        DB::transaction(function () use ($titulo, $parcela, $tituloService) {
            $tituloService->update([
                'valor_total' => $titulo->valor_total - $parcela->valor
            ], $titulo->id); 
            
            $this->update([
                'valor' => 0,
                'status' => 'cancelado'
            ], $parcela->id);
        });

        return ['status' => true, 'message' => 'Desconto aplicado com suscesso.'];
    }
}