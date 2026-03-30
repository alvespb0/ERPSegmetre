<?php

namespace App\Livewire\Titulo\ContasReceber;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Parcela;
use App\Models\TituloFinanceiro;

use App\Services\ContaService;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\FormaPagamentoService;
use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;
use App\Services\MovimentacaoService;

use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

/**
 * Class ListTitulo
 *
 * Componente Livewire responsável por listar e gerenciar os títulos financeiros
 * do tipo contas a receber, incluindo filtros, paginação e visualização de detalhes.
 */
class ListTitulo extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $openModalDetalhesParcela = false;
    public ?Parcela $parcelaSelecionada = null;

    public $openModalDetalhesTitulo = false;
    public ?TituloFinanceiro $tituloSelecionado = null;

    public bool $openModalReceberParcela = false;
    public ?Parcela $parcelaAReceber = null;

    public bool $openModalEditarParcela = false;
    public ?Parcela $parcelaParaEditar = null;

    public $editDataVencimento;
    public $editDescricao;
    public $editDataEmissao;
    public $editNumeroNf;
    public $editCategoriaId;
    public $editCentroCustoId;
    public $editContaId;
    public $editObservacoes;

    /* Variáveis da modal de lançamento de movimentação */
    public $formasPagamento;
    public $pagamentoData;
    public $pagamentoValor;
    public $pagamentoFormaId = '';

    public $search = '';
    public $filtroCompetencia;
    public $filtroCard;
    public $contas, $categorias, $centrosCusto;

    /* ['ontem', 'hoje'] filtros: */
    public $filtroDiaEspecifico;
    public $labelDiaEspecifico;

    /* semana filtros: */
    public $inicioSemana;
    public $fimSemana;

    /* Mes filtros: */
    public $filtroMesAno;
    public $labelMesAno;

    /* Range filtros: */
    public $dataInicioRange;
    public $dataFimRange;

    /**
     * Método executado ao montar o componente.
     * Carrega dados iniciais necessários para filtros.
     *
     * @param ContaService $contaService
     * @param CategoriaFinanceiraService $categoriaFinanceiraService
     * @param CentroCustoService $centroCustoService
     * @return void
     */
    public function mount(ContaService $contaService, CategoriaFinanceiraService $categoriaFinanceiraService, CentroCustoService $centroCustoService, FormaPagamentoService $formasPagamentoService){
        $this->contas = $contaService->show();
        $this->categorias = $categoriaFinanceiraService->showReceitas();
        $this->centrosCusto = $centroCustoService->show();
        $this->formasPagamento = $formasPagamentoService->show();
    }
    
    public function updated($property){
        $this->resetPage();
    }

    /**
     * Atualiza os filtros de data conforme o tipo selecionado.
     *
     * @return void
     */
    public function updatedFiltroCompetencia(){
        $this->resetarFiltrosDeData();
        switch ($this->filtroCompetencia){
            case 'hoje':
                $this->filtroDiaEspecifico = Carbon::today();
                $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
                break;
            case 'ontem':
                $this->filtroDiaEspecifico = Carbon::yesterday();
                $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
                break;
            case 'semana':
                $this->inicioSemana = Carbon::now()->startOfWeek();
                $this->fimSemana = Carbon::now()->endOfWeek();
                break;
            case 'mes':
                $this->filtroMesAno = Carbon::now()->format('Y-m');
                $this->labelMesAno = Carbon::parse($this->filtroMesAno . '-01')->format('m/Y');
                break;
            case 'custom':
                $this->dataInicioRange = Carbon::now()->startOfMonth()->toDateString();
                $this->dataFimRange = Carbon::now()->endOfMonth()->toDateString();
                break;
            default:
                break;
        }
    }

    /**
     * Limpa todos os filtros aplicados.
     *
     * @return void
     */
    public function limparFiltros(){
        $this->resetarFiltrosDeData();
        $this->search = '';
        $this->filtroCard = '';
        $this->filtroCompetencia = '';
    }

    /**
     * Reseta todos os filtros de data.
     *
     * @return void
     */
    public function resetarFiltrosDeData(){
        $this->filtroDiaEspecifico = null;
        $this->labelDiaEspecifico = null;
        $this->inicioSemana = null;
        $this->fimSemana = null;
        $this->filtroMesAno = null;
        $this->labelMesAno = null;
        $this->dataInicioRange = null;
        $this->dataFimRange = null;
    }

    /**
     * Retrocede um dia no filtro de data específica.
     *
     * @return void
     */
    public function diaAnterior(){
        $this->filtroDiaEspecifico->subDay();
        $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
    }

    /**
     * Avança um dia no filtro de data específica.
     *
     * @return void
     */
    public function diaPosterior(){
        $this->filtroDiaEspecifico->addDay();
        $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
    }


    /**
     * Retrocede um mês no filtro de mês/ano.
     *
     * @return void
     */
    public function mesAnterior(){
        $this->filtroMesAno = Carbon::parse($this->filtroMesAno . '-01')->subMonth()->format('Y-m');
        $this->labelMesAno = Carbon::parse($this->filtroMesAno . '-01') ->format('m/Y');
    }

    /**
     * Avança um mês no filtro de mês/ano.
     *
     * @return void
     */
    public function mesPosterior(){
        $this->filtroMesAno = Carbon::parse($this->filtroMesAno . '-01')->addMonth()->format('Y-m');
        $this->labelMesAno = Carbon::parse($this->filtroMesAno . '-01') ->format('m/Y');
    }
    /* FIM FILTOR DE DATAS */

    /**
     * Aplica filtro baseado no card selecionado.
     *
     * @param string $value
     * @return void
     */
    public function filtrarPorCard($value){
        $this->filtroCard = $value;
    }

    /**
     * Aplica todos os filtros na query de parcelas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function aplicarFiltros($query){
        if($this->filtroDiaEspecifico){
            $data = $this->filtroDiaEspecifico->toDateString();
            $query->whereDate('data_vencimento', $data);
        }

        if($this->inicioSemana && $this->fimSemana){
            $query->whereBetween('data_vencimento', [$this->inicioSemana, $this->fimSemana]);
        }
        
        if($this->filtroMesAno){
            $query->whereYear('data_vencimento', substr($this->filtroMesAno, 0, 4))
                ->whereMonth('data_vencimento', substr($this->filtroMesAno, 5, 2));
        }
        
        if($this->dataInicioRange && $this->dataFimRange){
            $query->whereBetween('data_vencimento', [$this->dataInicioRange, $this->dataFimRange]);
        }

        if($this->search){
            $query->where(function($query){
                $query->whereHas('titulo.entidade', function($q){
                        $q->where('razao_social', 'like', '%' . $this->search . '%')
                        ->orWhere('cpf_cnpj', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('titulo', function($q){
                        $q->where('numero_nf', 'like', '%' . $this->search . '%')
                        ->orWhere('descricao', 'like', '%' . $this->search . '%')
                        ->orWhere('observacoes', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('valor', 'like', '%' . $this->search . '%');
            });
        }

        if($this->filtroCard){
            switch($this->filtroCard){
                case 'aberto':
                    $query->where('status', '!=', 'cancelado')
                        ->whereDate('data_vencimento', '>=', now());
                        break;
                case 'atrasado':
                    $query->where('status', '!=', 'cancelado')
                        ->whereDate('data_vencimento', '<', now());
                        break;
                case 'hoje':
                    $query->where('status', '!=', 'cancelado')
                        ->whereDate('data_vencimento', now());
                    break;
                case 'pago':
                    $query->where('status', '!=', 'cancelado')
                        ->whereHas('movimentacoes', function ($q) {
                            $q->selectRaw('parcela_id, SUM(valor_pago) as total_pago')
                            ->groupBy('parcela_id')
                            ->havingRaw('SUM(valor_pago) >= parcelas.valor');
                        });
                    break;
                default:
                    break;
            }
        }

        return $query;
    }

    /**
     * Abre o modal com detalhes do título financeiro.
     *
     * @param TituloFinanceiro $titulo
     * @return void
     */
    public function verDetalhesTitulo(TituloFinanceiro $titulo){
        $titulo->load([
            'entidade', 
            'categoriaFinanceira', 
            'centroCusto', 
            'conta.banco', 
            'parcelas.movimentacoes'
        ]);

        $this->tituloSelecionado = $titulo;
        $this->openModalDetalhesTitulo = true;
    }

    /**
     * Abre o modal com detalhes de uma parcela.
     *
     * @param Parcela $parcela
     * @return void
     */
    public function detalhesParcela(Parcela $parcela){
        $this->parcelaSelecionada = $parcela;

        $this->openModalDetalhesParcela = true;
    }

    /**
     * Abre o modal de recebimento de parcela.
     *
     * @param Parcela $parcela
     * @return void
     */
    public function receberParcela(Parcela $parcela){
        $this->parcelaAReceber = $parcela;
        
        $this->pagamentoData = today()->format('Y-m-d');
        $this->pagamentoValor = $parcela->saldo_devedor;
        $this->pagamentoFormaId = '';
        
        $this->openModalReceberParcela = true;
    }

    public function salvarRecebimento(MovimentacaoService $movimentacaoService){
        $this->validate([
            'pagamentoData' => 'required|date',
            'pagamentoValor' => 'required|numeric|min:0.01|max:' . $this->parcelaAReceber->saldo_devedor, // Evita pagar mais que o devido
            'pagamentoFormaId' => 'required|exists:forma_pagamento,id',
        ], [
            'pagamentoData.required' => 'A data do pagamento é obrigatória.',
            'pagamentoData.date' => 'Informe uma data de pagamento válida.',

            'pagamentoValor.required' => 'O valor do pagamento é obrigatório.',
            'pagamentoValor.numeric' => 'O valor do pagamento deve ser um número.',
            'pagamentoValor.min' => 'O valor do pagamento deve ser maior que zero.',
            'pagamentoValor.max' => 'O valor pago não pode ser maior que o saldo devedor.',

            'pagamentoFormaId.required' => 'A forma de pagamento é obrigatória.',
            'pagamentoFormaId.exists' => 'A forma de pagamento selecionada é inválida.',
        ]);

        $movimentacaoService->store([
            'forma_pagamento_id' => $this->pagamentoFormaId ?? null,
            'parcela_id' => $this->parcelaAReceber->id,
            'valor_pago' => $this->pagamentoValor,
            'data_pagamento' => $this->pagamentoData
        ]);

        $this->openModalReceberParcela = false;
        $this->reset(['parcelaAReceber', 'pagamentoData', 'pagamentoValor', 'pagamentoFormaId']);
        
        $this->dispatch('toast-message', 'Pagamento lançado com sucesso!');
    }

    public function excluirMovimentacao(MovimentacaoService $movimentacaoService, $id){
        $movimentacaoService->destroy($id);

        $this->dispatch('toast-message', 'Movimentação excluída com sucesso');
    }

    public function editarParcela(Parcela $parcela){
        $parcela->load('titulo.entidade');
        $this->parcelaParaEditar = $parcela;

        $titulo = $parcela->titulo;

        // Popula os campos editáveis
        $this->editDataVencimento = $parcela->data_vencimento;
        $this->editDescricao = $titulo->descricao;
        $this->editDataEmissao = $titulo->data_emissao;
        $this->editNumeroNf = $titulo->numero_nf;
        $this->editCategoriaId = $titulo->categoria_financeira_id;
        $this->editCentroCustoId = $titulo->centro_custo_id;
        $this->editContaId = $titulo->conta_id;
        $this->editObservacoes = $titulo->observacoes;

        $this->openModalEditarParcela = true;
    }

    public function salvarEdicao(ParcelaService $parcelaService, TituloFinanceiroService $tituloService){
        $this->validate([
            'editDataVencimento' => 'required|date',
            'editDescricao' => 'required|string|max:255',
            'editDataEmissao' => 'required|date',
            'editNumeroNf' => 'nullable|string|max:50',
            'editCategoriaId' => 'nullable|exists:categoria_financeira,id',
            'editCentroCustoId' => 'nullable|exists:centro_custo,id',
            'editContaId' => 'nullable|exists:conta,id',
            'editObservacoes' => 'nullable|string',
        ], [
            'editDataVencimento.required' => 'A data de vencimento é obrigatória.',
            'editDataVencimento.date' => 'Informe uma data de vencimento válida.',

            'editDescricao.required' => 'A descrição é obrigatória.',
            'editDescricao.string' => 'A descrição deve ser um texto válido.',
            'editDescricao.max' => 'A descrição pode ter no máximo 255 caracteres.',

            'editDataEmissao.required' => 'A data de emissão é obrigatória.',
            'editDataEmissao.date' => 'Informe uma data de emissão válida.',

            'editNumeroNf.string' => 'O número da nota fiscal deve ser um texto válido.',
            'editNumeroNf.max' => 'O número da nota fiscal pode ter no máximo 50 caracteres.',

            'editCategoriaId.exists' => 'A categoria selecionada é inválida.',

            'editCentroCustoId.exists' => 'O centro de custo selecionado é inválido.',

            'editContaId.exists' => 'A conta selecionada é inválida.',

            'editObservacoes.string' => 'As observações devem ser um texto válido.',
        ]);
        
        $parcelaService->update([
            'data_vencimento' => $this->editDataVencimento
        ], $this->parcelaParaEditar->id);

        $tituloService->update([
            'descricao' => $this->editDescricao,
            'data_emissao' => $this->editDataEmissao,
            'numero_nf' => $this->editNumeroNf,
            'categoria_financeira_id' => $this->editCategoriaId ?? null,
            'centro_custo_id' => $this->editCentroCustoId ?? null,
            'conta_id' => $this->editContaId ?? null,
            'observacoes' => $this->editObservacoes,
        ], $this->parcelaParaEditar->titulo->id);

        $this->openModalEditarParcela = false;
        $this->reset([
            'parcelaParaEditar', 'editDataVencimento', 'editDescricao', 'editDataEmissao', 
            'editNumeroNf', 'editCategoriaId', 'editCentroCustoId', 'editContaId', 'editObservacoes'
        ]);
        
        $this->dispatch('toast-message', 'Título/Parcela atualizada com sucesso!');
    }

    /**
     * Renderiza o componente com os dados filtrados e métricas.
     *
     * @return \Illuminate\View\View
     */
    public function render(){
        $query = $this->aplicarFiltros(Parcela::query());

        $queryBase = clone $query;

        $vencidos = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereHas('titulo', function ($q) {
                                    $q->where('tipo', 'receber');
                                })
                                ->whereDate('data_vencimento', '<', now())
                                ->sum('valor');

        $abertos = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereHas('titulo', function ($q) {
                                    $q->where('tipo', 'receber');
                                })
                                ->whereDate('data_vencimento', '>=', now())
                                ->sum('valor');

        $venceHoje = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereHas('titulo', function ($q) {
                                    $q->where('tipo', 'receber');
                                })
                                ->whereDate('data_vencimento', now())
                                ->sum('valor');

        $pagos = (clone $queryBase)->whereHas('titulo', function ($q) {
                                    $q->where('tipo', 'receber');
                                })->get()->sum('valor_pago');

        $parcelas = $query->with(['titulo' => function ($q) { $q->withCount('parcelas'); }])
                            ->whereHas('titulo', function ($q) {
                                $q->where('tipo', 'receber');
                            })
                            ->orderBy('data_vencimento', 'asc')->paginate(10);

        return view('livewire.titulo.contas-receber.list-titulo', [
            'parcelas' => $parcelas,
            'vencidos' => $vencidos,
            'abertos' => $abertos,
            'venceHoje' => $venceHoje,
            'pagos' => $pagos
        ]);
    }
}
