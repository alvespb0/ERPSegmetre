<?php

namespace App\Livewire\Titulo\ContasReceber;

use Livewire\Component;
use Carbon\Carbon;

use App\Services\ContaService;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\EntidadeService;
use App\Services\FormaPagamentoService;
use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;

use App\Models\Parcela;

class ListTitulo extends Component
{
    public $search = '';
    public $periodoFiltro = '';

    public function render(ContaService $contaService,
        CategoriaFinanceiraService $categoriaFinanceiraService,
        CentroCustoService $centroCustoService,
        EntidadeService $entidadeService,
        FormaPagamentoService $formaPagamentoService,
        ParcelaService $parcelaService,
    )
    {
        $contas = $contaService->show();
        $categorias = $categoriaFinanceiraService->showReceitas();
        $centrosCusto = $centroCustoService->show();
        $entidades = $entidadeService->showClientes();
        $formasPagamento = $formaPagamentoService->show();

        $parcelas = Parcela::orderBy('data_vencimento', 'asc')->paginate(10);

        return view('livewire.titulo.contas-receber.list-titulo', [
            'contas' => $contas,
            'categorias' => $categorias,
            'centrosCusto' => $centrosCusto,
            'entidades' => $entidades,
            'formasPagamento' => $formasPagamento,
            'parcelas' => $parcelas
        ]);
    }
}
