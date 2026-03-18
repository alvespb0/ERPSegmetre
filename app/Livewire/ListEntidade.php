<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Entidade;
use Livewire\WithPagination;

class ListEntidade extends Component
{
    use WithPagination;

    public $search = '';
    public $tipo = 'todos';
    public $status = 'todos';
    public $initialPage;

    public function updatingSearch(){
        $this->resetPage();
    }

    public function inativarEntidade($id){
        
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
        
        if($this->status != 'todos'){
            $query->when($this->status == 'inativo', function($q){
                $q->whereNotNull('deleted_at');
            });

            $query->when($this->status == 'ativo', function($q){
                $q->whereNull('deleted_at');
            });
        }

        $entidades = $query->orderBy('razao_social', 'asc')->paginate(10);
        
        return view('livewire.entidades.list-entidade', [
            'entidades' => $entidades
        ]);
    }
}
