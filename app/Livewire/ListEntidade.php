<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Entidade;
use App\Services\EntidadeService;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

/**
 * Componente Livewire responsável pela listagem e gerenciamento de Entidades.
 * * Este componente inclui funcionalidades de paginação (sem refletir na URL),
 * filtros de busca por texto (Razão Social, Nome Fantasia ou CNPJ/CPF),
 * filtro por tipo (classificação) e status (ativos, inativos ou todos usando Soft Deletes).
 */
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

    /**
     * Inativa uma entidade específica (Soft Delete) utilizando o serviço correspondente.
     * Dispara um evento de toast ao concluir.
     *
     * @param int|string $id ID da entidade a ser inativada.
     * @return void
     */
    public function inativarEntidade($id){
        $service = new EntidadeService();

        $service->destroy($id);

        $this->dispatch('toast-message', 'Entidade inativada com sucesso!');
    }

    /**
     * Reativa uma entidade que estava inativada utilizando o serviço correspondente.
     * Dispara um evento de toast ao concluir.
     *
     * @param int|string $id ID da entidade a ser reativada.
     * @return void
     */
    public function ativarEntidade($id){
        $service = new EntidadeService();

        $service->restore($id);

        $this->dispatch('toast-message', 'Entidade reativada com sucesso!');
    }

    /**
     * Criptografa o ID da entidade selecionada e redireciona o usuário para a rota de edição.
     *
     * @param int|string $id ID da entidade a ser editada.
     * @return \Illuminate\Http\RedirectResponse Redirecionamento para a view de atualização.
     */
    public function editarEntidade($id){
        $idEnc = Crypt::encrypt($id);

        redirect()->route('erp.entidades.update', $idEnc);
    }
    
    /**
     * Constrói a query de listagem aplicando os filtros de busca, tipo e status.
     * Retorna a coleção paginada para a view.
     *
     * @return \Illuminate\View\View
     */
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
