<?php

namespace App\Livewire\FormaPagamento;

use Livewire\Component;
use App\Models\FormaPagamento;
use App\Services\FormaPagamentoService;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

class ListFormaPagamento extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $status = 'todos';

    public function updatingSearch(){
        $this->resetPage();
    }

    public function inativarFormaPagamento($id){
        $service = new FormaPagamentoService();

        $service->destroy($id);
    
        $this->dispatch('toast-message', 'Forma de pagamento inativada com sucesso');
    }

    public function ativarFormaPagamento($id){
        $service = new FormaPagamentoService();

        $service->restore($id);
    
        $this->dispatch('toast-message', 'Forma de pagamento reativada com sucesso');
    }

    public function editarFormaPagamento($id){
        $idEnc = Crypt::encrypt($id);

        redirect()->route('erp.forma-pagamento.update', $idEnc);
    }


    public function render()
    {
        $query = FormaPagamento::query();

        if($this->search){
            $query->where('nome', 'like', '%' . $this->search . '%');
        }

        $query->when($this->status == 'inativo', function($q){
            $q->onlyTrashed('deleted_at');
        });

        $query->when($this->status == 'ativo', function($q){
            $q->whereNull('deleted_at');
        });
        
        $query->when($this->status == 'todos', function($q){
            $q->withTrashed();
        });

        $formasPag = $query->orderBy('nome', 'asc')->paginate(10);
        return view('livewire.forma-pagamento.list-forma-pagamento', ['formasPagamento' => $formasPag]);
    }
}
