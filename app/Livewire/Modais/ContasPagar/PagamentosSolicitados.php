<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use App\Helpers\Empresa;

use App\Models\SolicitacoesPagamento;
use App\Models\Conta;
use App\Models\Movimentacao;

class PagamentosSolicitados extends Component
{
    public $solicitacao;
    public $contas;
    public $selected_conta;
    public $saldo, $limite, $bloqueado;

    public function mount($solicitacaoId){
        $this->solicitacao = SolicitacoesPagamento::findOrFail($solicitacaoId);
        $this->contas = Conta::whereHas('configuracaoCobranca')->with('banco', 'tipoConta', 'configuracaoCobranca')->get(); 
    }

    /**
     * Ao fazer a seleção da conta bancária para pagamento tenta resgatar o saldo
     * Primeiro tenta via integração bancária, verificando se o método getSaldoProducao existe
     * Se não existir cai em callback puxando as Movimentacoes vinculadas a conta
     */
    public function updatedSelectedConta(){
        $this->buscarSaldoConta($this->selected_conta);
    }

    private function buscarSaldoConta($conta_id){
        try{
            $conta = Conta::findOrFail($conta_id);
            $config = $conta->configuracaoCobranca;
            $resultado = [];

            if(!$config->integracao){
                $resultado = $this->buscaSaldoMovimentacoes($conta);
            }else{
                $resultado = $this->buscaSaldoIntegracao($config, $conta);
            }

            $this->saldo = $resultado['saldo'];
            $this->limite = $resultado['limite'];
            $this->bloqueado = $resultado['bloqueado'];
        }catch(\Throwable $e){
            \Log::error([
                'Erro ao buscar saldo da conta' => $e->getMessage(),
                'Conta' => $conta_id,
                'Empresa Parâmetro' => Empresa::id()
            ]);

            $this->dispatch('toast-error', 'Erro ao resgatar saldo da conta');
        }
    }

    private function buscaSaldoIntegracao($config, Conta $conta){
        $integracao = $config->integracao;        
        $factory = new \App\Factories\IntegracaoFactory;
        $serviceProvider = $factory->make($integracao, 'cco');

        if ($config->ambiente === 'homologacao') {

            if (!method_exists($serviceProvider, 'getSaldoSandbox')) {
                $this->dispatch('toast-error', 'Integração não implementa Saldo SANDBOX.');
                return;
            }

            return $serviceProvider->getSaldoSandbox(preg_replace('/-/', '', $conta->conta));
        } elseif ($config->ambiente === 'producao') {

            if (!method_exists($serviceProvider, 'getSaldoProducao')) {
                $this->dispatch('toast-error', 'Integração não implementa saldo.');
                return;
            }

            return $serviceProvider->getSaldoProducao(preg_replace('/-/', '', $conta->conta));
        }       
    }

    private function buscaSaldoMovimentacoes(Conta $conta){
        $saidas = Movimentacao::whereHas('parcela.titulo', function ($q){
            $q->where('tipo', 'pagar');
        })
        ->where('conta_id', $conta->id)->sum('valor_pago');

        $entradas = Movimentacao::whereHas('parcela.titulo', function ($q){
            $q->where('tipo', 'receber');
        })
        ->where('conta_id', $conta->id)->sum('valor_pago');

        $saldo = $entradas - $saidas;

        return [
            'saldo' => $saldo,
            'limite' => 0.00,
            'bloqueado' => 0.00,
            'origem' => 'movimentacoes'
        ];
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.pagamentos-solicitados');
    }
}
