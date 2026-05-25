<?php

namespace App\Livewire\Conta;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Models\Conta;
use App\Services\ContaService;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

class ListConta extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $modalidade = 'todas';
    public $status = 'todos';

    public ?Conta $contaSelecionada = null;
    public $openModalConfigCobranca = false;

    public function updatingSearch(){
        $this->resetPage();
    }

    public function inativarConta($id){
        $service = new ContaService();

        $service->destroy($id);

        $this->dispatch('toast-message', 'Conta inativada com sucesso!');
    }

    public function ativarConta($id){
        $service = new ContaService();

        $service->restore($id);

        $this->dispatch('toast-message', 'Conta reativada com sucesso!');
    }

    public function editarConta($id){
        $idEnc = Crypt::encrypt($id);

        redirect()->route('erp.conta.update', $idEnc);
    }

    public function abrirConfigCobranca(Conta $conta){
        $this->contaSelecionada = $conta;
        $this->openModalConfigCobranca = true;
    }
    
    #[On('fechar-modal-configuracao-bancaria')]
    public function fecharModalAnexos(){
        $this->openModalConfigCobranca = false;

        $this->contaSelecionada = null;
    }

    public function render()
    {
        $query = Conta::query();

        if($this->search){
            $query->where('nome', 'like', '%' . $this->search . '%')
                ->orWhere('agencia', 'like', '%' . $this->search . '%')
                ->orWhere('conta', 'like', '%' . $this->search . '%')
                ->orWhereHas('banco', function($q){
                    $q->where('nome', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('tipoConta', function($q){
                    $q->where('descricao', 'like', '%' . $this->search . '%');
                });
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
           
        if (in_array($this->modalidade, ['pf', 'pj'])){
            $query->where('modalidade', $this->modalidade);
        }

        $contas = $query->with([
                'banco',
                'tipoConta',
                'configuracaoCobranca',
                ])->orderBy('nome', 'asc')->paginate(10);

        return view('livewire.conta.list-conta', ['contas' => $contas]);
    }
}
