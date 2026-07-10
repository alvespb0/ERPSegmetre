<?php

namespace App\Livewire\SOC;

use Livewire\Component;

use App\Services\EntidadeService;
use App\Services\IntegracaoSocEmpresaService;

use App\Models\Integracao;
use App\Models\IntegracaoSocEmpresa;
use App\Models\Entidade;

class ValorizacaoSoc extends Component
{
    public $integracao;
    public $dataInicio, $dataFim;
    public $examesValorizados = [];
    public $empresasSoc = [];

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

            $this->empresasSoc = collect($serviceProvider->getEmpresasSoc())->keyBy('CODIGO')->toArray();

            if(!$this->empresasSoc){
                throw new \Exception('Erro ao resgatar empresas SOC cojunto vazio.');
            }
        }catch (\Exception $e){
            \Log::error([
                'Erro ao resgatar empresas soc' => $e->getMessage()
            ]);

            $this->dispatch('toast-error', 'Erro ao resgatar empresas SOC');
        }
    }

    public function vincularEmpresa($codEmpresa, $codUnidade = null){
        $entidadeService = new EntidadeService;
        $empresaSocService = new IntegracaoSocEmpresaService;

        foreach($this->empresasSoc as $empresaSoc){
            if($codEmpresa == $empresaSoc['CODIGO']){
                $entidade = Entidade::where('cpf_cnpj', preg_replace('/[.\/-]/', '', $empresaSoc['CNPJ']))->first();
                if(!$entidade){
                    $cnpj = !$codUnidade ? preg_replace('/[.\/-]/', '', $empresaSoc['CNPJ']): null;

                    $entidade = $entidadeService->store([
                        'razao_social' => $empresaSoc['RAZAOSOCIAL'] ?? $empresaSoc['NOMEABREVIADO'],
                        'cpf_cnpj' => $cnpj, # sim a integracao nao traz cpf haha
                        'tipo' => 'pj', # ja que a itnegracao nao traz cpf nem adianta eu fazer qualquer validacao,
                        'classificacao' => 'cliente',
                    ]);
                }
                $empresaSocService->store([
                    'entidade_id' => $entidade->id,
                    'codigo_empresa' => $codEmpresa,
                    'codigo_unidade' => $codUnidade ?? null,
                    'nome_unidade' => $entidade->razao_social ?? null
                ]);
                
                break;
            }
        }

        foreach ($this->examesValorizados as &$item) {
            if (
                $item['CODIGO_EMPRESA'] == $codEmpresa &&
                ($item['CODIGO_UNIDADE'] ?: null) == $codUnidade
            ) {
                $item['vinculada'] = true;
                $item['entidade_id'] = $entidade->id;
            }
        }

        $this->dispatch('toast-message', 'Empresa vinculada com sucesso');
    }

    public function render()
    {
        return view('livewire.s-o-c.valorizacao-soc');
    }
}
