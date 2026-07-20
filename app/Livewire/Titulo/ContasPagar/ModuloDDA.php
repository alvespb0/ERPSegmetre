<?php

namespace App\Livewire\Titulo\ContasPagar;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Models\Conta;
use App\Models\Integracao;
use App\Models\SolicitacoesPagamento;

class ModuloDDA extends Component
{
    public $contas;
    public ?int $selectedConta = null;
    public $integracao = null;
    public $filtroConta, $dataInicial, $dataFinal, $situacao;
    public $titulosVinculados = [];
    public $titulosSemVinculo = [];

    public $openModalDespesa = false;
    public $dadosDDA = [];
    
    public function mount(){
        $this->contas = Conta::whereHas('configuracaoCobranca')->with('banco', 'tipoConta', 'configuracaoCobranca')->get();
    }

    /**
     * Consulta os boletos DDA da conta selecionada utilizando
     * a integração configurada para o ambiente correspondente.
     *
     * @return void
     */
    public function buscarBoletos(){
        $this->titulosSemVinculo = [];
        $this->titulosVinculados = [];

        $conta = Conta::findOrFail($this->selectedConta);

        $config = $conta->configuracaoCobranca;

        if(!$config->integracao){
            $this->dispatch('toast-error', 'Conta selecionada não possui integracao ou nao possui modulo de DDA.');
            return;
        }

        $this->integracao = $config->integracao;

        try{
            $factory = new \App\Factories\IntegracaoFactory;
            $serviceProvider = $factory->make($this->integracao, 'pagamento');

            $titulos = [];
            $titulosVinculados = [];
            $titulosSemVinculo = [];

            if ($config->ambiente === 'homologacao') {
                if (!method_exists($serviceProvider, 'ddaSandbox')) {
                    $this->dispatch('toast-error', 'Integração não implementa DDA Sandbox.');
                    return;
                }
                $titulos = $serviceProvider->ddaSandbox($this->dataInicial, $this->dataFinal, $this->situacao, preg_replace('/-/', '', $conta->conta));
            } elseif ($config->ambiente === 'producao') {
                if (!method_exists($serviceProvider, 'ddaProducao')) {
                    $this->dispatch('toast-error', 'Integração não implementa DDA.');
                    return;
                }
                $titulos = $serviceProvider->ddaProducao($this->dataInicial, $this->dataFinal, $this->situacao, preg_replace('/-/', '', $conta->conta));
            }
            $linhas = collect($titulos)
                ->pluck('linha_digitavel')
                ->all();

            $vinculados = SolicitacoesPagamento::whereIn('identificador', $linhas)
                ->pluck('identificador')
                ->flip();

            foreach($titulos as $titulo){
                if (isset($vinculados[$titulo['linha_digitavel']])) {
                    $titulosVinculados[] = $titulo;
                } else {
                    $titulosSemVinculo[] = $titulo;
                }
            }

            $this->titulosSemVinculo = $titulosSemVinculo;
            $this->titulosVinculados = $titulosVinculados;

            $this->dispatch('toast-message', 'Boletos resgatados com sucesso.');
        }catch (\Throwable $e){
            \Log::error([
                'Erro ao resgatar cobrancas DDA' => $e->getMessage(),
                'Empresa parametro' => $conta->empresa_parametro_id,
                'Conta' => $this->selectedConta
            ]);

            $this->dispatch('toast-error', 'Erro ao resgatar cobrancas DDA.');
        }
    }
    
    public function cadastrarDespesa($linhaDigitavel){
        $titulo = collect($this->titulosSemVinculo)->firstWhere('linha_digitavel', $linhaDigitavel);
        $this->dadosDDA = $titulo;
        $this->openModalDespesa = true;
    }

    /**
     * Evento acionado para fechar o modal de anexos e limpar os dados.
     * * @return void
     */
    #[On('fechar-modal-cadastro-despesa')]
    public function fecharModalAnexos(){
        $this->openModalDespesa = false;

        $this->dadosDDA = null;
    }

    public function render()
    {
        return view('livewire.titulo.contas-pagar.modulo-d-d-a');
    }
}
