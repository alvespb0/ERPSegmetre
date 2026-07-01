<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;

use App\Services\BoletoCobrancaService;

use App\Models\Parcela;
use App\Models\BoletoCobranca;

class CancelarCobranca extends Component
{
    public $parcela;
    public $boletoAtivo;

    public function mount($parcelaId){
        $this->parcela = Parcela::with(['titulo.entidade', 'boletos'])->findOrFail($parcelaId);
        $this->parcela->titulo->loadCount('parcelas');
        $this->boletoAtivo = $this->parcela->boleto_ativo;
    }

    /**
     * Dispara um evento para o front-end indicando o fechamento do modal de cancelar cobrança.
     *
     * @return void
     */
    public function fecharModal(){
        $this->dispatch('fechar-modal-cancela-cobranca');
    }

    public function cancelar(){
        try{
            $factory = new \App\Factories\IntegracaoFactory;
            $serviceProvider = $factory->make($this->boletoAtivo->configuracaoCobranca->integracao, 'cobranca');

            $retorno = $serviceProvider->cancelarBoletoProducao($this->boletoAtivo);

            $this->boletoAtivo->update([
                'status' => $retorno['status']
            ]);

            $this->dispatch('toast-message', 'Boleto cancelado com sucesso!.');
            $this->dispatch('fechar-modal-cancela-cobranca');
        }catch (\Throwable $e){
            \Log::error([
                'Erro ao cancelar boleto' => [
                    'boleto_id' => $this->boletoAtivo->id,
                    'erro' => $e->getMessage(),
                ]
            ]);
            $this->dispatch('toast-error', 'Erro ao cancelar cobrança, tente novamente mais tarde.');
            $this->dispatch('fechar-modal-cancela-cobranca');
        }
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.cancelar-cobranca');
    }
}
