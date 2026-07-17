<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\EntidadeService;
use App\Services\FormaPagamentoService;
use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;


class LancarTituloDDA extends Component
{
    public $categoriasFinanceira, $centrosCusto, $entidades, $formasPagamento;

    public function mount(
        CategoriaFinanceiraService $categoriaFinanceiraService,
        CentroCustoService $centroCustoService,
        EntidadeService $entidadeService,
        FormaPagamentoService $formaPagamentoService
    ){
        $this->categoriasFinanceira = $categoriaFinanceiraService->showDespesas();
        $this->centrosCusto = $centroCustoService->show();
        $this->entidades = $entidadeService->showFornecedores();
        $this->formasPagamento = $formaPagamentoService->show();
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.lancar-titulo-d-d-a');
    }
}
