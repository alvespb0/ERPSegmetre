<?php

namespace App\Livewire\Titulo\ContasReceber;

use Maatwebsite\Excel\Facades\Excel;

use Livewire\Component;
use Livewire\Attributes\On;

use Carbon\Carbon;

use App\Models\Parcela;
use App\Models\TituloFinanceiro;

use App\Exports\TitulosExport;

use App\Services\ContaService;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\FormaPagamentoService;
use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;
use App\Services\MovimentacaoService;
use App\Services\BoletoCobrancaService;

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

    public $selecionados = [];

    public $openModalDetalhesParcela = false;
    public ?Parcela $parcelaSelecionada = null;

    public $openModalDetalhesTitulo = false;
    public ?TituloFinanceiro $tituloSelecionado = null;

    public bool $openModalReceberParcela = false;
    public ?Parcela $parcelaAReceber = null;

    public bool $openModalEditarParcela = false;
    public ?Parcela $parcelaParaEditar = null;

    public bool $openModalEditarStatus = false;
    public ?Parcela $parcelaParaEditarStatus = null;

    public bool $openModalAnexos = false;
    public ?Parcela $parcelaParaAnexos = null;

    public bool $openModalCobranca = false;
    public ?Parcela $parcelaParaCobranca = null;

    public bool $openModalCobrancaLote = false;
    public array $parcelasCobrancaLote = [];

    public bool $openModalCancelaCobranca = false;
    public ?Parcela $parcelaParaCancelaCobranca = null;

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
     * Evento acionado para fechar o modal de detalhe de titulos e limpar os dados.
     * * @return void
     */
    #[On('fechar-modal-titulo')]
    public function fecharModalTitulo(){
        $this->openModalDetalhesTitulo = false;

        $this->tituloSelecionado = null;
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
                
        $this->openModalReceberParcela = true;
    }
    
    /**
     * Evento acionado para fechar o modal de recebimento e limpar os dados.
     * * @return void
     */
    #[On('fechar-modal-recebimento')]
    public function fecharModalRecebimento(){
        $this->openModalReceberParcela = false;

        $this->parcelaAReceber = null;
    }

    /**
     * Abre o modal para edição de uma parcela específica.
     * * @param Parcela $parcela
     * @return void
     */
    public function editarParcela(Parcela $parcela){
        $parcela->load('titulo.entidade');
        $this->parcelaParaEditar = $parcela;

        $this->openModalEditarParcela = true;
    }

    /**
     * Evento acionado para fechar o modal de edição e limpar os dados.
     * * @return void
     */
    #[On('fechar-modal-edicao')]
    public function fecharModalEdicao(){
        $this->openModalEditarParcela = false;

        $this->parcelaParaEditar = null;
    }
    
    /**
     * Abre o modal para alteração rápida de status de uma parcela.
     * * @param Parcela $parcela
     * @return void
     */
    public function editarStatus(Parcela $parcela){
        $parcela->load('titulo.entidade');
        $this->parcelaParaEditarStatus = $parcela;
        $this->openModalEditarStatus = true;
    }

    /**
     * Evento acionado para fechar o modal de status e limpar os dados.
     * * @return void
     */
    #[On('fechar-modal-status')]
    public function fecharModalStatus(){
        $this->openModalEditarStatus = false;

        $this->parcelaParaEditarStatus = null;
    }

    /**
     * Abre o modal de anexos de uma parcela, carregando relacionamentos necessários.
     * * @param Parcela $parcela
     * @return void
     */
    public function gerarCobrancaParcela(Parcela $parcela){
        $this->parcelaParaCobranca = $parcela;

        $this->openModalCobranca = true;
    }

    /**
     * Evento acionado para fechar o modal de anexos e limpar os dados.
     * * @return void
     */
    #[On('fechar-modal-cobranca')]
    public function fecharModalCobranca(){
        $this->openModalCobranca = false;

        $this->parcelaParaCobranca = null;
    }

    /**
     * Evento acionado para fechar o modal de anexos e limpar os dados.
     * * @return void
     */
    #[On('abrir-modal-cobranca-lote')]
    public function openModalCobrancaLote(array $parcelas){
        $this->parcelasCobrancaLote = $parcelas;

        $this->openModalCobrancaLote = true;
    }

    /**
     * Abre o modal de cancelar cobranca da parcela, carregando relacionamentos necessários.
     * * @param Parcela $parcela
     * @return void
     */
    public function cancelarCobrancaParcela(Parcela $parcela){
        if(!$parcela->possui_boleto_ativo){
            $this->dispatch('toast-error', 'Parcela não possui um boleto ativo.');
            return;
        }

        $parcela->load([
                'titulo.entidade',
                'boletos',
            ]);
            
        $this->parcelaParaCancelaCobranca = $parcela;

        $this->openModalCancelaCobranca = true;
    }

    /**
     * Evento acionado para fechar o modal de anexos e limpar os dados.
     * * @return void
     */
    #[On('fechar-modal-cancela-cobranca')]
    public function fecharModalCancelaCobranca(){
        $this->openModalCancelaCobranca = false;

        $this->parcelaParaCancelaCobranca = null;
    }

    /**
     * Abre o modal de anexos de uma parcela, carregando relacionamentos necessários.
     * * @param Parcela $parcela
     * @return void
     */
    public function anexosParcela(Parcela $parcela){
        $parcela->load([
                'titulo', 
                'anexos', 
                'movimentacoes', 
            ]);
            
        $this->parcelaParaAnexos = $parcela;

        $this->openModalAnexos = true;
    }

    /**
     * Evento acionado para fechar o modal de anexos e limpar os dados.
     * * @return void
     */
    #[On('fechar-modal-anexos')]
    public function fecharModalAnexos(){
        $this->openModalAnexos = false;

        $this->parcelaParaAnexos = null;
    }

    /**
     * Acessa boleto cobranca service e retorna o download do boleto em PDF
     */
    public function downloadCobranca($boletoId){
        try{
            $service = new BoletoCobrancaService();
                
            return $service->download($boletoId);
        }catch(\Exception $e){
            \Log::error([
                'Erro ao fazer download do boleto',
                'Boleto' => $boletoId,
                'Erro' => $e->getMessage()
            ]);
            $this->dispatch('toast-error', 'Erro ao fazer download do boleto.');
        }
    }

    private function getQuery(){
        $query = $this->aplicarFiltros(Parcela::query());

        return $query->whereHas('titulo', function ($q) {
            $q->where('tipo', 'receber');
        });
    }

    public function exportar(){
        if(!empty($this->selecionados)){
            return Excel::download(new TitulosExport($this->selecionados), 'relatorio_lancamentos.xlsx');
        }else{
            $query = $this->getQuery();
            
            $query->with([
                'titulo.entidade',
                'titulo.centroCusto',
                'titulo.categoriaFinanceira',
                'movimentacoes'
            ]);

            return Excel::download(new TitulosExport($this->selecionados, $query), 'relatorio_lancamentos.xlsx');
        }
    }

    /**
     * Renderiza o componente com os dados filtrados e métricas.
     *
     * @return \Illuminate\View\View
     */
    public function render(){
        $query = $this->getQuery();

        $queryBase = clone $query;

        $vencidos = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereDate('data_vencimento', '<', now())
                                ->with('movimentacoes')
                                ->get()
                                ->filter(function ($parcela) {
                                    return $parcela->valor_pago < $parcela->valor;
                                })
                                ->sum(function ($parcela) { 
                                    return $parcela->valor - $parcela->valor_pago;
                                });

        $abertos = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereDate('data_vencimento', '>=', now())
                                ->with('movimentacoes')
                                ->get()
                                ->filter(function ($parcela) {
                                    return $parcela->valor_pago < $parcela->valor;
                                })
                                ->sum(function ($parcela) {
                                    return $parcela->valor - $parcela->valor_pago;
                                });

        $venceHoje = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereDate('data_vencimento', now())
                                ->sum('valor');

        $pagos = (clone $queryBase)->with('movimentacoes')
                                ->get()->sum('valor_pago');

        $parcelas = $query->with(['titulo' => function ($q) { $q->withCount('parcelas'); }])
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
