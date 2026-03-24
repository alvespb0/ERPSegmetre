<?php

namespace App\Livewire\Titulo\ContasReceber;

use Livewire\Component;
use App\Services\ContaService;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\EntidadeService;
use App\Services\FormaPagamentoService;

class CreateTitulo extends Component
{
    public $entidade_id, $descricao, $status, $valor_total, $data_vencimento, $quantidade_parcelas;

    public function gerarParcelas(){
        dd($this->valor_total);
    }

    public function render(
        ContaService $contaService,
        CategoriaFinanceiraService $categoriaFinanceiraService,
        CentroCustoService $centroCustoService,
        EntidadeService $entidadeService,
        FormaPagamentoService $formaPagamentoService
    )
    {
        $contas = $contaService->show();
        $categorias = $categoriaFinanceiraService->showReceitas();
        $centrosCusto = $centroCustoService->show();
        $entidades = $entidadeService->show();
        $formasPagamento = $formaPagamentoService->show();

        return view('livewire.titulo.contas-receber.create-titulo', [
            'contas' => $contas,
            'categoriasFinanceira' => $categorias,
            'centrosCusto' => $centrosCusto,
            'entidades' => $entidades,
            'formasPagamento' => $formasPagamento,
        ]);

    }
}
