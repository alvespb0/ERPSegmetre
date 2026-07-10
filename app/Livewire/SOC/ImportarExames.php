<?php

namespace App\Livewire\SOC;

use Illuminate\Support\Facades\DB;

use Livewire\Component;

use Carbon\Carbon;

use App\Models\IntegracaoSocEmpresa;
use App\Models\Entidade;

class ImportarExames extends Component
{
    public $exames, $centrosCusto, $categorias;
    public $categoria_id, $centro_custo_id;

    public function mount($exames){
        $centroCustoService = new \App\Services\CentroCustoService;
        $categoriaService = new \App\Services\CategoriaFinanceiraService;
        $this->exames = $exames;
        $this->centrosCusto = $centroCustoService->show();
        $this->categorias = $categoriaService->showReceitas();
    }

    public function fechar(){
        $this->dispatch('fechar-modal-soc');
    }

    public function importar(){
        $tituloService = new \App\Services\TituloFinanceiroService;
        $parcelaService = new \App\Services\ParcelaService;

        $sucessoCount = 0;
        $erros = [];
        foreach($this->exames as $exame){
            try{
                DB::beginTransaction();
                $entidade = Entidade::find($exame['entidade_id']);
                $valor = (float) str_replace(['.', ','], ['', '.'], $exame['VALOR_TOTAL']);
                $titulo = $tituloService->store([
                    'centro_custo_id' => $this->centro_custo_id ?? null,
                    'categoria_financeira_id' => $this->categoria_id ?? null,
                    'entidade_id' => $entidade->id,
                    'descricao' => 'Exame valorizado e importado pelo SOC',
                    'observacoes' => 'Origem SOC',
                    'valor_total' => $valor,
                    'data_emissao' => Carbon::today(),
                    'tipo' => 'receber',
                    'status' => 'ativo'
                ]);

                $diaVencimento = $entidade->dia_vencimento_padrao ?? 1;
                $mesVencimento = $exame['MES_COBRANCA'];

                $parcela = $parcelaService->store([
                    'titulo_financeiro_id' => $titulo->id,
                    'numero_parcela' => 1,
                    'valor' => $valor,
                    'data_vencimento' => $this->formatarDataVencimento($diaVencimento, $mesVencimento),
                    'status' => 'ativo'
                ]);
                DB::commit();
                $sucessoCount++;
            }catch(\Exception $e){
                DB::rollBack();
                \Log::error([
                    'empresa' => $exame['EMPRESA'],
                    'codigo_empresa' => $exame['CODIGO_EMPRESA'],
                    'codigo_unidade' => $exame['CODIGO_UNIDADE'],
                    'erro' => $e->getMessage(),
                ]);
                $erros = ['Não foi possível importar o exame da empresa ' . $exame['EMPRESA']];
            }
        }

        if($sucessoCount > 0){
            $this->dispatch('toast-message', 'Importado ' . $sucessoCount . ' exames');
        }

        if(!empty($erros)){
            foreach($erros as $erro){
                $this->dispatch('toast-error', $erro);
            }
        }
    }

    public function formatarDataVencimento($dia, $mes){
        $data = Carbon::create(now()->year, $mes, $dia);

        return $data->toDateString();
    }

    public function render()
    {
        return view('livewire.s-o-c.importar-exames');
    }
}
