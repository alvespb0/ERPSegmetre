<?php

namespace App\Livewire\CentroCusto;

use Livewire\Component;
use App\Models\CentroCusto;
use App\Services\CentroCustoService;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

class ListCentroCusto extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $status = 'todos';

    public function updatingSearch(){
        $this->resetPage();
    }

    public function inativarCentro($id){
        $service = new CentroCustoService();

        $service->destroy($id);

        $this->dispatch('toast-message', 'Centro de Custo inativado com sucesso!');
    }

    public function ativarCentro($id){
        $service = new CentroCustoService();

        $service->restore($id);

        $this->dispatch('toast-message', 'Centro de Custo reativado com sucesso!');
    }

    /**
     * Criptografa o ID da centro custo selecionada e redireciona o usuário para a rota de edição.
     *
     * @param int|string $id ID do centro custo a ser editada.
     * @return \Illuminate\Http\RedirectResponse Redirecionamento para a view de atualização.
     */
    public function editarCentro($id){
        $idEnc = Crypt::encrypt($id);

        redirect()->route('erp.centro-custo.update', $idEnc);
    }
    

    public function render(){
        $query = CentroCusto::query();
        
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

        $centrosCusto = $query->orderBy('nome', 'asc')->paginate(10);

        return view('livewire.centro-custo.list-centro-custo', ['centrosCusto' => $centrosCusto]);
    }
}
