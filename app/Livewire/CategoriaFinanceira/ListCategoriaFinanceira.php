<?php

namespace App\Livewire\CategoriaFinanceira;

use Livewire\Component;
use App\Models\CategoriaFinanceira;
use App\Services\CategoriaFinanceiraService;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

class ListCategoriaFinanceira extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $tipo = 'todos';
    public $status = 'todos';

    public function inativarCategoria($id){
        $service = new CategoriaFinanceiraService();

        $service->destroy($id);

        $this->dispatch('toast-message', 'Categoria Financeira inativada com sucesso!');
    }
    
    public function ativarCategoria($id){
        $service = new CategoriaFinanceiraService();

        $service->restore($id);

        $this->dispatch('toast-message', 'Categoria Financeira reativada com sucesso!');

    }

    public function editarCategoria($id){
        $idEnc = Crypt::encrypt($id);

        redirect()->route('erp.categoria-financeira.update', $idEnc);
    }

    public function render()
    {
        $query = CategoriaFinanceira::query();

        if($this->search){
            $query->where('nome', 'like', '%' . $this->search . '%')
                ->orWhere('descricao', 'like', '%' . $this->search . '%');
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
           
        if (in_array($this->tipo, ['receita', 'despesa'])){
            $query->where('tipo', $this->tipo);
        }

        $categorias = $query->orderBy('nome', 'asc')->paginate(10);

        return view('livewire.categoria-financeira.list-categoria-financeira', ['categorias' => $categorias]);
    }
}
