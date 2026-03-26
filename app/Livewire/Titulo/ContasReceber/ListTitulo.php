<?php

namespace App\Livewire\Titulo\ContasReceber;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Parcela;

use App\Services\ContaService;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\EntidadeService;
use App\Services\FormaPagamentoService;
use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;

use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Crypt;

class ListTitulo extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $openModalDetalhesParcela = false;
    public ?Parcela $parcelaSelecionada = null;

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

    public function mount(ContaService $contaService, CategoriaFinanceiraService $categoriaFinanceiraService, CentroCustoService $centroCustoService){
        $this->contas = $contaService->show();
        $this->categorias = $categoriaFinanceiraService->showReceitas();
        $this->centrosCusto = $centroCustoService->show();
    }

    /* FILTROS DE DATA */
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

    public function diaAnterior(){
        $this->filtroDiaEspecifico->subDay();
        $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
    }

    public function diaPosterior(){
        $this->filtroDiaEspecifico->addDay();
        $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
    }

    public function mesAnterior(){
        $this->filtroMesAno = Carbon::parse($this->filtroMesAno . '-01')->subMonth()->format('Y-m');
        $this->labelMesAno = Carbon::parse($this->filtroMesAno . '-01') ->format('m/Y');
    }

    public function mesPosterior(){
        $this->filtroMesAno = Carbon::parse($this->filtroMesAno . '-01')->addMonth()->format('Y-m');
        $this->labelMesAno = Carbon::parse($this->filtroMesAno . '-01') ->format('m/Y');
    }
    /* FIM FILTOR DE DATAS */

    /* FILTRO DE CARD */
    public function filtrarPorCard($value){
        $this->filtroCard = $value;
    }

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
            }
        }

        return $query;
    }

    public function detalhesParcela(Parcela $parcela){
        $this->parcelaSelecionada = $parcela;
        // 2. Você pode aproveitar para carregar relacionamentos aqui, se necessário:
        // $this->parcelaSelecionada->load('titulo.entidade', 'pagamentos');

        $this->openModalDetalhesParcela = true;
    }

    public function render(){
        $query = $this->aplicarFiltros(Parcela::query());

        $queryBase = clone $query;

        $vencidos = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereDate('data_vencimento', '<', now())
                                ->sum('valor');

        $abertos = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereDate('data_vencimento', '>=', now())
                                ->sum('valor');

        $venceHoje = (clone $queryBase)->where('status', '!=', 'cancelado')
                                ->whereDate('data_vencimento', now())
                                ->sum('valor');

        $pagos = (clone $queryBase)->get()->sum('valor_pago');

        $parcelas = $query->with(['titulo' => function ($q) { $q->withCount('parcelas'); }])->orderBy('data_vencimento', 'asc')->paginate(10);

        return view('livewire.titulo.contas-receber.list-titulo', [
            'parcelas' => $parcelas,
            'vencidos' => $vencidos,
            'abertos' => $abertos,
            'venceHoje' => $venceHoje,
            'pagos' => $pagos
        ]);
    }
}
