<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use App\Models\Parcela;

use App\Services\TituloFinanceiroService;
use App\Services\ParcelaService;

use Illuminate\Support\Facades\DB;

class EditarStatus extends Component
{
    public $parcela;
    public $novoStatus;
    public $escopoStatus;
    public $tipoAjuste;
    public $confirmarAjuste = false;
    
    public function mount($parcelaId){
        $this->parcela = Parcela::with('titulo.entidade', 'movimentacoes')->findOrFail($parcelaId);
    }

    private function aplicarDesconto(ParcelaService $parcelaService, TituloFinanceiroService $tituloService){
        $titulo = $this->parcela->titulo;

        DB::transaction(function () use ($titulo, $parcelaService, $tituloService) {
            $tituloService->update([
                'valor_total' => $titulo->valor_total - $this->parcela->valor
            ], $titulo->id); 
            
            $parcelaService->update([
                'valor' => 0,
                'status' => 'cancelado'
            ], $this->parcela->id);
        });

        $this->dispatch('fechar-modal-status');

        $this->dispatch('toast-message', 'Status alterado com sucesso!');
    }

    private function redistribuirParcela(ParcelaService $parcelaService){
        $numParcelasFaltantes = $this->parcela->titulo->parcelas_faltantes - 1; # -1 por que vai cancelar essa parcela
        if($numParcelasFaltantes <= 0){
            $this->dispatch('toast-error', 'Não há parcelas restantes para distribuir o saldo devedor da parcela.');
            return;
        }
        $saldoDevedor = $this->parcela->titulo->saldo_devedor;

        $titulo_id = $this->parcela->titulo->id;

        $dataVencInicial = $this->parcela->data_vencimento;

        DB::transaction(function () use ($numParcelasFaltantes, $saldoDevedor, $dataVencInicial, $titulo_id, $parcelaService) {
            /* Parcela atual vinda do mount passa a ter valor zerado e status cancelado */
            $parcelaService->update(['status' => 'cancelado'], $this->parcela->id);

            /* Pega todas as parcelas restantes desse titulo, transforma em collection com get, e filtra as que não estão com status dinamico pago */
            $parcelasAtuais = Parcela::where('titulo_financeiro_id', $titulo_id)
                ->get()
                ->filter(function ($parcela) {
                    return $parcela->status_calculado != 'pago' && $parcela->status_calculado != 'cancelado';
                });

            /* Gerado novas parcelas para reaproveitar lógica de centavos */
            $novasParcelas = $parcelaService->gerarParcelas($saldoDevedor, $numParcelasFaltantes, $dataVencInicial);

            foreach ($parcelasAtuais->values() as $index => $parcelaAtual) {
                if (!isset($novasParcelas[$index])) {
                    continue;
                }
                $parcelaService->update([
                    'valor' => $novasParcelas[$index]['valor_parcela'],
                    'status' => 'ativo'
                ], $parcelaAtual->id);
            }
        });

        $this->dispatch('fechar-modal-status');

        $this->dispatch('toast-message', 'Status alterado com sucesso!');
    }

    private function cancelarTitulo(TituloFinanceiroService $tituloService){
        if($this->parcela->titulo->saldo_devedor != $this->parcela->titulo->valor_total){ # só dá para marcar como cancelado na condição de que titulo nao tenha movimentações geradas
            $this->dispatch('toast-error', 'Não foi possível cancelar o título, o mesmo já possui movimentações realizadas.');
            return;
        }

        $titulo = $this->parcela->titulo;
        DB::transaction(function () use ($titulo, $tituloService) {
            $tituloService->update([
                'status' => 'cancelado'
            ], $titulo->id); 
            
            $titulo->parcelas()->update([
                'status' => 'cancelado'
            ]);
        });

        $this->dispatch('fechar-modal-status');

        $this->dispatch('toast-message', 'Status alterado com sucesso!');
    }

    public function salvarStatus(TituloFinanceiroService $tituloService, ParcelaService $parcelaService){
        try{
            if($this->novoStatus == 'cancelado' && $this->escopoStatus == 'parcela'){
                if(!$this->tipoAjuste){
                    $this->addError('tipoAjuste', 'Selecione uma opção de ajuste para continuar.');
                    return;
                }
                
                if($this->tipoAjuste == 'desconto'){
                    $this->aplicarDesconto($parcelaService, $tituloService);
                }

                if($this->tipoAjuste == 'redistribuir'){
                    $this->redistribuirParcela($parcelaService);
                }
                
            }

            if($this->novoStatus == 'cancelado' && $this->escopoStatus == 'titulo'){
                $this->cancelarTitulo($tituloService);
            }

        }catch(\Exception $e){
            $this->dispatch('fechar-modal-status');

            $this->dispatch('toast-error', 'Erro ao alterar status da parcela.');
            \Log::error("Erro ao Alterar Status da Parcela: ", ['erro' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.editar-status');
    }
}
