<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Entidade;
use App\Services\EntidadeService;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class ListEntidade extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $tipo = 'todos';
    public $status = 'todos';
    public $initialPage;

    public function updatingSearch(){
        $this->resetPage();
    }

    public function inativarEntidade($id){
        $service = new EntidadeService();

        $service->destroy($id);

        $this->dispatch('toast-message', 'Entidade inativada com sucesso!');
    }

    public function ativarEntidade($id){
        $service = new EntidadeService();

        $service->restore($id);

        $this->dispatch('toast-message', 'Entidade reativada com sucesso!');
    }

    public function render(){
        $query = Entidade::query();
        
        if($this->search){
            $query->where('razao_social', 'like', '%' . $this->search . '%')
                ->orWhere('nome_fantasia', 'like', '%' . $this->search . '%')
                ->orWhere('cpf_cnpj', 'like', '%' . $this->search . '%');
        }

        if($this->tipo != 'todos'){
            $query->where('classificacao', $this->tipo);
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

        $entidades = $query->orderBy('razao_social', 'asc')->paginate(10);
        
        return view('livewire.entidades.list-entidade', [
            'entidades' => $entidades
        ]);
    }
}
