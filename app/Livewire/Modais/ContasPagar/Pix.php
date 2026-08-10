<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use App\Models\Conta;

class Pix extends Component
{
    public $contas;
    public $selected_conta;
    public $saldo, $limite, $bloqueado;

    public $tipo_pix;
    public $valor;
    public $identificador;
    
    public function mount(){
        $this->contas = Conta::whereHas('configuracaoCobranca')->with('banco', 'tipoConta', 'configuracaoCobranca')->get(); 
    }

    /**
     * Ao fazer a seleção da conta bancária para pagamento tenta resgatar o saldo
     * Primeiro tenta via integração bancária, verificando se o método getSaldoProducao existe
     * Se não existir cai em callback puxando as Movimentacoes vinculadas a conta
     * 
     * @return void
     */
    public function updatedSelectedConta(){
        if (!$this->selected_conta) {
            $this->reset([
                'saldo',
                'limite',
                'bloqueado',
            ]);

            return;
        }

        $this->buscarSaldoConta($this->selected_conta);
    }

    /**
     * Consulta o saldo da conta bancária.
     *
     * Caso exista integração bancária configurada, utiliza a API do banco.
     * Caso contrário, calcula o saldo com base nas movimentações financeiras.
     *
     * @param int $conta_id ID da conta bancária.
     * @return void
     */
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

    /**
     * Obtém o saldo da conta através da integração bancária.
     *
     * A consulta utiliza o ambiente configurado (homologação ou produção)
     * e verifica se o provider implementa o método correspondente.
     *
     * @param mixed $config Configuração da cobrança da conta.
     * @param Conta $conta Conta bancária.
     * @return array|null Retorna um array contendo saldo, limite e bloqueado,
     *                    ou null caso a integração não implemente o método.
     */
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

    /**
     * Calcula o saldo da conta com base nas movimentações financeiras.
     *
     * O saldo é calculado pela diferença entre as entradas (receber)
     * e as saídas (pagar).
     *
     * @param Conta $conta Conta bancária.
     * @return array{
     *     saldo: float,
     *     limite: float,
     *     bloqueado: float,
     *     origem: string
     * }
     */
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

    public function executarPix(){
        dd($this->tipo_pix);
    }
    public function render()
    {
        return view('livewire.modais.contas-pagar.pix');
    }
}
