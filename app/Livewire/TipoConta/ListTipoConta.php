<?php

namespace App\Livewire\TipoConta;

use Livewire\Component;
use App\Models\TipoConta;
use App\Services\TipoContaService;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

class ListTipoConta extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $status = 'todos';

    public function updatingSearch(){
        $this->resetPage();
    }

    public function editarTipoConta($id){
        $idEnc = Crypt::encrypt($id);

        redirect()->route('erp.tipo-conta.update', $idEnc);
    }

    public function inativarTipoConta($id){
        $service = new TipoContaService();

        $service->destroy($id);
    
        $this->dispatch('toast-message', 'Tipo de Conta inativada com sucesso');
    }

    public function ativarTipoConta($id){
        $service = new TipoContaService();

        $service->restore($id);
    
        $this->dispatch('toast-message', 'Tipo de Conta reativada com sucesso');
    }
    
    public function render()
    {
        $query = TipoConta::query();

        if($this->search){
            $query->where('descricao', 'like', '%' . $this->search . '%');
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

        $tiposConta = $query->orderBy('descricao', 'asc')->paginate(2);

        return view('livewire.tipo-conta.list-tipo-conta', ['tiposConta' => $tiposConta]);
    }
}
