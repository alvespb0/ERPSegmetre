<?php

namespace App\Livewire\Banco;

use Livewire\Component;
use App\Models\Banco;
use App\Services\BancoService;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

class ListBanco extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $status = 'todos';

    public function inativarBanco($id){
        $service = new BancoService();

        $service->destroy($id);

        $this->dispatch('toast-message', 'Banco inativado com sucesso!');
    }

    public function ativarBanco($id){
        $service = new BancoService();

        $service->restore($id);

        $this->dispatch('toast-message', 'Banco reativado com sucesso!');
    }

    public function editarBanco($id){
        $idEnc = Crypt::encrypt($id);

        redirect()->route('erp.banco.update', $idEnc);
    }

    public function updatingSearch(){
        $this->resetPage();
    }

    public function render(){
        $query = Banco::query();

        if($this->search){
            $query->where('nome', 'like', '%' . $this->search . '%')
                ->orWhere('cnpj', 'like', '%' . $this->search . '%');
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

        $bancos = $query->orderBy('nome', 'asc')->paginate(10);

        return view('livewire.banco.list-banco', ['bancos' => $bancos]);
    }
}
