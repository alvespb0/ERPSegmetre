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

    public function salvarStatus(TituloFinanceiroService $tituloService, ParcelaService $parcelaService){
        try{
            if($this->escopoStatus == 'parcela'){                
                $retorno = $parcelaService->alterarStatusParcela($this->parcela, $this->novoStatus, $this->tipoAjuste);

                if($retorno['status'] === true){
                    $this->dispatch('fechar-modal-status');

                    $this->dispatch('toast-message', $retorno['message']);
                }

                if($retorno['status'] === false){
                    $this->dispatch('fechar-modal-status');

                    $this->dispatch('toast-error', $retorno['message']);
                }
            }

            if($this->escopoStatus == 'titulo'){
                $retorno = $tituloService->alterarStatusTitulo($this->parcela, $this->novoStatus);

                if($retorno['status'] === true){
                    $this->dispatch('fechar-modal-status');

                    $this->dispatch('toast-message', $retorno['message']);
                }

                if($retorno['status'] === false){
                    $this->dispatch('fechar-modal-status');

                    $this->dispatch('toast-error', $retorno['message']);
                }
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
