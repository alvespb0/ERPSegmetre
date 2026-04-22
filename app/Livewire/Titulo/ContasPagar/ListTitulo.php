<?php

namespace App\Livewire\Titulo\ContasPagar;

use Livewire\Component;
use Livewire\Attributes\On;

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

class ListTitulo extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $openModalDetalhesParcela = false;
    public ?Parcela $parcelaSelecionada = null;

    public $openModalDetalhesTitulo = false;
    public ?TituloFinanceiro $tituloSelecionado = null;

    public bool $openModalPagarParcela = false;
    public ?Parcela $parcelaParaPagar = null;

    public bool $openModalEditarParcela = false;
    public ?Parcela $parcelaParaEditar = null;

    public bool $openModalEditarStatus = false;
    public ?Parcela $parcelaParaEditarStatus = null;

    public bool $openModalAnexos = false;
    public ?Parcela $parcelaParaAnexos = null;

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
        $this->categorias = $categoriaFinanceiraService->showDespesas();
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
                        ->whereDate('data_vencimento', '>=', now())
                        ->whereDoesntHave('movimentacoes', function ($q) {
                            $q->selectRaw('parcela_id')
                                ->groupBy('parcela_id')
                                ->havingRaw('SUM(valor_pago) > 0');
                        });
                    break;
                case 'atrasado':
                    $query->where('status', '!=', 'cancelado')
                        ->whereDate('data_vencimento', '<', now())
                        ->whereDoesntHave('movimentacoes', function ($q) {
                            $q->selectRaw('parcela_id')
                                ->groupBy('parcela_id')
                                ->havingRaw('SUM(valor_pago) > 0');
                        });
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
     * Abre o modal de pagamento de parcela.
     *
     * @param Parcela $parcela
     * @return void
     */
    public function pagarParcela(Parcela $parcela){
        $this->parcelaParaPagar = $parcela;
                
        $this->openModalPagarParcela = true;
    }

    #[On('fechar-modal-pagamento')]
    public function fecharModalPagamento(){
        $this->openModalPagarParcela = false;

        $this->parcelaParaPagar = null;
    }

    public function editarParcela(Parcela $parcela){
        $parcela->load('titulo.entidade');
        $this->parcelaParaEditar = $parcela;

        $this->openModalEditarParcela = true;
    }

    #[On('fechar-modal-edicao')]
    public function fecharModalEdicao(){
        $this->openModalEditarParcela = false;

        $this->parcelaParaEditar = null;
    }
    
    public function editarStatus(Parcela $parcela){
        $parcela->load('titulo.entidade');
        $this->parcelaParaEditarStatus = $parcela;
        $this->openModalEditarStatus = true;
    }

    #[On('fechar-modal-status')]
    public function fecharModalStatus(){
        $this->openModalEditarStatus = false;

        $this->parcelaParaEditarStatus = null;
    }

    public function anexosParcela(Parcela $parcela){
        $parcela->load([
                'titulo', 
                'anexos', 
                'movimentacoes', 
            ]);
            
        $this->parcelaParaAnexos = $parcela;

        $this->openModalAnexos = true;
    }

    #[On('fechar-modal-anexos')]
    public function fecharModalAnexos(){
        $this->openModalAnexos = false;

        $this->parcelaParaAnexos = null;
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
                                    $q->where('tipo', 'pagar');
                                })
                                ->whereDate('data_vencimento', '<', now())
                                ->get()
                                ->filter(function ($parcela) {
                                    return $parcela->valor_pago < $parcela->valor;
                                })
                                ->sum(function ($parcela) { 
                                    return $parcela->valor - $parcela->valor_pago;
                                });

        $abertos = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereHas('titulo', function ($q) {
                                    $q->where('tipo', 'pagar');
                                })
                                ->whereDate('data_vencimento', '>=', now())
                                ->get()
                                ->filter(function ($parcela) {
                                    return $parcela->valor_pago < $parcela->valor;
                                })
                                ->sum(function ($parcela) {
                                    return $parcela->valor - $parcela->valor_pago;
                                });

        $venceHoje = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereHas('titulo', function ($q) {
                                    $q->where('tipo', 'pagar');
                                })
                                ->whereDate('data_vencimento', now())
                                ->sum('valor');

        $pagos = (clone $queryBase)->whereHas('titulo', function ($q) {
                                    $q->where('tipo', 'pagar');
                                })->get()->sum('valor_pago');

        $parcelas = $query->with(['titulo' => function ($q) { $q->withCount('parcelas'); }])
                            ->whereHas('titulo', function ($q) {
                                $q->where('tipo', 'pagar');
                            })
                            ->orderBy('data_vencimento', 'asc')->paginate(10);

        return view('livewire.titulo.contas-pagar.list-titulo', [
            'parcelas' => $parcelas,
            'vencidos' => $vencidos,
            'abertos' => $abertos,
            'venceHoje' => $venceHoje,
            'pagos' => $pagos
        ]);
    }
}
