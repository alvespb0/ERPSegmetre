<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use Carbon\Carbon;

use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use App\Models\Parcela;

use App\Services\SolicitacoesPagamentoService;

class SolicitacaoPagamento extends Component
{
    public $parcela;
    public $tipo = '';
    public $identificador;
    public $valor;
    
    public function mount($parcelaId){
        $this->parcela = Parcela::with(['titulo' => function ($q) { $q->withCount('parcelas');}, 'solicitacoesPagamento'])->findOrFail($parcelaId);
        $this->valor = $this->parcela->saldo_devedor;
    }

    public function fechar(){
        $this->dispatch('fechar-modal-solicitacao-pagamento');
    }

    public function rules(){
        return [
            'tipo' => 'required|in:codigo_barras,pix,pix_copia_cola,tributo',
            'identificador' => [
                'required',
                Rule::when(
                    $this->tipo === 'codigo_barras',
                    ['min:43', 'max:48']
                ),
            ],
            'valor' => 'required|numeric|min:1'
        ];
    }

    public function messages(){
        return [
            'tipo.required' => 'O campo tipo é obrigatório.',
            'tipo.in' => 'O tipo informado é inválido.',

            'identificador.required' => 'O identificador é obrigatório.',
            'identificador.min' => 'O código de barras deve possuir no mínimo 43 caracteres.',
            'identificador.max' => 'O código de barras deve possuir no máximo 48 caracteres.',

            'valor.required' => 'O valor é obrigatório.',
            'valor.decimal' => 'O valor deve ser um número decimal válido.',
            'valor.min' => 'O valor deve ser maior que zero.',
        ];
    }

    public function salvarSolicitacao(){
        try{
            $this->validate();
            $service = new SolicitacoesPagamentoService;

            if ($this->parcela->status === 'cancelado') {
                $this->dispatch('toast-error', 'Não é possível lançar solicitação de pagamento para parcela cancelada.');
                return;
            }
            if($this->parcela->possuiSolicitacaoPagamento){
                $this->dispatch('toast-error', 'Parcela já possui uma solicitação de pagamento pendente.');
                return;
            }

            if($this->parcela->saldo_devedor == 0 || $this->parcela->saldo_devedor < $this->valor){
                $this->dispatch('toast-error', 'Saldo devedor já zerado ou valor à ser pago é maior que saldo devedor.');
                return;
            }

            $service->store([
                'parcela_id' => $this->parcela->id,
                'tipo' => $this->tipo,
                'identificador' => $this->identificador,
                'valor' => $this->valor,
                'data_solicitacao' => Carbon::now()
            ]);

            $this->parcela->load('solicitacoesPagamento');
            $this->reset(['tipo', 'identificador']);
            $this->valor = $this->parcela->saldo_devedor;
            $this->dispatch('toast-message', 'Solicitacao de pagamento lançada com sucesso');

        } catch (ValidationException $e) {
            throw $e; 
        } catch(\Throwable $e){
            \Log::error([
                    'Erro ao lancar solicitacao de pagamento' => $e->getMessage(),
                ]);
            $this->dispatch('toast-error', 'Erro ao lançar solicitação de pagamento.');
            $this->fechar();
        }
    }

    public function cancelarSolicitacao($solicitacaoId){
        $service = new SolicitacoesPagamentoService;

        $service->update($solicitacaoId, [
            'status' => 'cancelado'
        ]);

        $this->dispatch('toast-message', 'Solicitação cancelada com sucesso');
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.solicitacao-pagamento');
    }
}
