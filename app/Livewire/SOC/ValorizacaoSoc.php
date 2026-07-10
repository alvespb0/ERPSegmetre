<?php

namespace App\Livewire\SOC;

use Livewire\Component;

use App\Models\Integracao;
use App\Models\IntegracaoSocEmpresa;

class ValorizacaoSoc extends Component
{
    public $integracao;
    public $dataInicio, $dataFim;
    public $examesValorizados = [];
    public $empresasSoc = [];
    public $openModalEmpresas = false;

    public function mount(){
        $this->integracao = Integracao::where('slug', 'soc-exames-producao')->firstOrFail();
        $this->getEmpresas();
    }

    /**
     * Consulta os exames valorizados no SOC e verifica
     * quais registros possuem vínculo cadastrado no sistema.
     *
     * O processo consiste em:
     * - Buscar os exames valorizados na integração.
     * - Extrair as chaves compostas (empresa + unidade).
     * - Consultar os vínculos existentes.
     * - Criar um mapa para busca rápida.
     * - Adicionar aos exames os campos:
     *   - vinculada (bool)
     *   - entidade_id (int|null)
     *
     * @return void
     */
    public function getValorizacoes(){
        try{
            $factory = new \App\Factories\IntegracaoFactory;
            $serviceProvider = $factory->make($this->integracao, 'exames');

            $this->examesValorizados = $serviceProvider->getFaturamento($this->dataInicio, $this->dataFim);

            # basicamente pega a array bruta de exames valorizados, e parseia para uma array de codigo_empresa e codigo_unidade
            $chaves = collect($this->examesValorizados)
                ->map(fn ($item) => [
                    'codigo_empresa' => (int) $item['CODIGO_EMPRESA'],
                    'codigo_unidade' => $item['CODIGO_UNIDADE'] !== ''
                        ? (int) $item['CODIGO_UNIDADE']
                        : null,
                ])
                ->unique()
                ->values();
            
            # da pluck em codigo_empresa
            $codigosEmpresa = $chaves->pluck('codigo_empresa')->unique();
            $vinculos = IntegracaoSocEmpresa::whereIn('codigo_empresa', $codigosEmpresa)->get(); # faz a  query whereIn (como nao da pra ser composta faz inicialemtne só com codigo empresa)

            $mapa = [];

            foreach ($vinculos as $vinculo) {
                $key = $vinculo->codigo_empresa . '|' . ($vinculo->codigo_unidade ?? ''); # faz uma chave composta string separado por |
                $mapa[$key] = $vinculo;
            }

            foreach ($this->examesValorizados as &$item) {
                $key = $item['CODIGO_EMPRESA'] . '|' . $item['CODIGO_UNIDADE']; # faz a mesma coisa aqui, pega a array de exames valorizados e transforma em uma chave composta separado por |
                $item['vinculada'] = isset($mapa[$key]); 
                $item['entidade_id'] = $mapa[$key]->entidade_id ?? null;
            }

            $this->dispatch('toast-message', 'Valorizacao resgatada com sucesso!');
        }catch (\Exception $e){
            \Log::error([
                'Erro ao resgatar valorizacao soc' => $e->getMessage()
            ]);

            $this->dispatch('toast-error', 'Erro ao resgatar valorizacao SOC');
        }
    }
    
    /**
     * Obtém a lista de empresas disponíveis no SOC.
     *
     * @return void
     */
    public function getEmpresas(){
        try{
            $integracao = Integracao::where('slug', 'soc-empresas-producao')->firstOrFail();
            $factory = new \App\Factories\IntegracaoFactory;
            $serviceProvider = $factory->make($integracao, 'empresas');

            $this->empresasSoc = $serviceProvider->getEmpresasSoc();

            if(empty($this->empresasSoc)){
                throw new \Exception('Erro ao resgatar empresas SOC cojunto vazio.');
            }
        }catch (\Exception $e){
            \Log::error([
                'Erro ao resgatar empresas soc' => $e->getMessage()
            ]);

            $this->dispatch('toast-error', 'Erro ao resgatar empresas SOC');
        }
    }

    public function render()
    {
        return view('livewire.s-o-c.valorizacao-soc');
    }
}
