<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

use App\Helpers\Empresa;

use App\Models\Conta;
use App\Models\Movimentacao;

use App\Services\SolicitacoesPagamentoService;

class Pix extends Component
{
    public $contas;
    public $selected_conta;
    public $saldo, $limite, $bloqueado;

    public $tipo_pix;
    public $valor;
    public $identificador;
    
    public $consultaPixRet = [];
    
    public function mount(){
        $this->contas = Conta::whereHas('configuracaoCobranca')->with('banco', 'tipoConta', 'configuracaoCobranca')->get(); 
    }

    public function fechar(){
        $this->dispatch('fechar-modal-pix');
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
                'consultaPixRet',
                'tipo_pix',
                'valor',
                'identificador'
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

    public function iniciaPix(){
        try{
            $this->validate(
                [
                    'selected_conta' => ['required'],
                    'tipo_pix'       => ['required', 'in:chave,copia_cola'],
                    'identificador'  => ['required', 'string', 'max:500'],
                    'valor'          => ['required', 'numeric', 'gt:0'],
                ],
                [
                    'selected_conta.required' => 'Selecione a conta de origem.',
                    'tipo_pix.required'       => 'Selecione o tipo de PIX.',
                    'tipo_pix.in'             => 'O tipo de PIX selecionado é inválido.',
                    'identificador.required'  => 'Informe a chave PIX ou o código Copia e Cola.',
                    'identificador.string'    => 'O identificador deve ser um texto válido.',
                    'identificador.max'       => 'O identificador não pode ter mais de 500 caracteres.',
                    'valor.required'          => 'Informe o valor do PIX.',
                    'valor.numeric'           => 'O valor precisa ser um número válido.',
                    'valor.gt'                => 'O valor do PIX deve ser maior que zero.',
                ]
            );

            $conta = Conta::findOrFail($this->selected_conta);

            $config = $conta->configuracaoCobranca;

            if(!$config->integracao){
                $this->dispatch('toast-error', 'A conta de origem não possui integração bancária para completar a transação.');
                return;
            }else{
                $integracao = $config->integracao;        
                $factory = new \App\Factories\IntegracaoFactory;
                $serviceProvider = $factory->make($integracao, 'pix_pagamento');

                $retorno = [];

                if ($config->ambiente === 'homologacao') {

                    if($this->tipo_pix == 'chave'){
                        if (!method_exists($serviceProvider, 'iniciaPixSandbox')) {
                            $this->dispatch('toast-error', 'Integração não implementa pix sandbox.');
                            return;
                        }

                        $retorno = $serviceProvider->iniciaPixSandbox($this->identificador);
                    }else if($this->tipo_pix == 'copia_cola'){
                        if (!method_exists($serviceProvider, 'iniciaPixQrCodeSandBox')) {
                            $this->dispatch('toast-error', 'Integração não implementa pix copia e cola sandbox.');
                            return;
                        }

                        $retorno = $serviceProvider->iniciaPixQrCodeSandBox($this->identificador);
                    }

                } elseif ($config->ambiente === 'producao') {

                    if($this->tipo_pix == 'chave'){
                        if (!method_exists($serviceProvider, 'iniciaPixProducao')) {
                            $this->dispatch('toast-error', 'Integração não implementa pix.');
                            return;
                        }

                        $retorno = $serviceProvider->iniciaPixProducao($this->identificador);

                    }else if($this->tipo_pix == 'copia_cola'){
                        if (!method_exists($serviceProvider, 'iniciaPixQrCodeProducao')) {
                            $this->dispatch('toast-error', 'Integração não implementa pix copia e cola.');
                            return;
                        }

                        $retorno = $serviceProvider->iniciaPixQrCodeProducao($this->identificador);
                    }

                }       

                $this->consultaPixRet = $retorno;
            }
        } catch (ValidationException $e) {
            throw $e; 
        } catch(\Throwable $e){
            \Log::error([
                'Erro ao realizar consulta de pix' => $e->getMessage(),
                'Conta' => $this->selected_conta,
                'Empresa Parâmetro' => Empresa::id()
            ]);

            $message = 'Erro ao processar pagamento.';

            if (method_exists($e, 'friendlyMessage')) {
                $message = $e->friendlyMessage();
            }

            $this->dispatch('toast-error', $message);
            $this->dispatch('toast-error', 'Verifique chave pix, saldo e horário de pagamento.');
        }
    }

    public function executarPix(){
        try{
            if(!$this->consultaPixRet){
                $this->dispatch('toast-error', 'Realize uma nova consutla de pix.');
                return;
            }

            $conta = Conta::findOrFail($this->selected_conta);

            $config = $conta->configuracaoCobranca;

            if(!$config->integracao){
                $this->dispatch('toast-error', 'A conta de origem não possui integração bancária para completar a transação.');
                return;
            }else{
                $integracao = $config->integracao;        
                $factory = new \App\Factories\IntegracaoFactory;
                $serviceProvider = $factory->make($integracao, 'pix_pagamento');

                $retorno = [];

                if ($config->ambiente === 'homologacao') {

                    if($this->tipo_pix == 'chave'){
                        if (!method_exists($serviceProvider, 'confirmaPixSandbox')) {
                            $this->dispatch('toast-error', 'Integração não implementa pix sandbox.');
                            return;
                        }

                        $retorno = $serviceProvider->confirmaPixSandbox();
                    }else if($this->tipo_pix == 'copia_cola'){
                        if (!method_exists($serviceProvider, 'confirmaPixQrCodeSandBox')) {
                            $this->dispatch('toast-error', 'Integração não implementa pix copia e cola sandbox.');
                            return;
                        }

                        $retorno = $serviceProvider->iniciaPixQrCodeSandBox();
                    }

                } elseif ($config->ambiente === 'producao') {

                    if($this->tipo_pix == 'chave'){
                        if (!method_exists($serviceProvider, 'confirmaPixProducao')) {
                            $this->dispatch('toast-error', 'Integração não implementa pix.');
                            return;
                        }

                        $retorno = $serviceProvider->confirmaPixProducao($this->consultaPixRet['e2eId'], $this->valor, $this->tipo_pix);

                    }else if($this->tipo_pix == 'copia_cola'){
                        if (!method_exists($serviceProvider, 'confirmaPixQrCodeProducao')) {
                            $this->dispatch('toast-error', 'Integração não implementa pix copia e cola.');
                            return;
                        }

                        $retorno = $serviceProvider->confirmaPixQrCodeProducao($this->identificador);
                    }

                }

                switch($retorno['status']){
                    case 'pago': 
                        $this->processarPagamentoEfetivado($retorno);
                        return;
                    case 'em_processamento':
                        $this->processarPagamentoEmAndamento($retorno);
                        return;
                    case 'recusado':
                        $this->dispatch('toast-error', $retorno['mensagem']);
                        return;
                    default:
                        $this->dispatch('toast-error', 'Instituição bancária não retornou status de pagamento.');
                        return;
                }
                
            }

        } catch(\Throwable $e){
            \Log::error([
                'Erro ao buscar tentar realizar transação para pagamento via pix' => $e->getMessage(),
                'Conta' => $this->selected_conta,
                'Empresa Parâmetro' => Empresa::id()
            ]);

            $message = 'Erro ao processar pagamento.';

            if (method_exists($e, 'friendlyMessage')) {
                $message = $e->friendlyMessage();
            }

            $this->dispatch('toast-error', $message);
            $this->dispatch('toast-error', 'Verifique chave pix, saldo e horário de pagamento.');
        }

    }

    /**
     * Lança solicitação sem parcela e sem movimentação, isso será essencial para aparecer na tela de 'pagamentos em conciliacao'
     * Após usuário conciliar com um titulo ele lança a movimentação (ja que parcela_id de mov nao pode ser null)
     */
    public function processarPagamentoEfetivado(array $retorno){
        $solicitacaoService = new SolicitacoesPagamentoService;

        $comp_path = $solicitacaoService->makeComprovantePagamento($retorno);

        $dataPagamento = \Carbon\Carbon::parse($retorno['pagamento']['data_pagamento'])->format('Ymd');

        $solicitacao = $solicitacaoService->store([
            'tipo' => $this->tipo_pix == 'chave' ? 'pix' : 'pix_copia_cola',
            'identificador' => $this->identificador,
            'conta_id' => $this->selected_conta,
            'valor' => $retorno['pagamento']['valor'],
            'data_pagamento' => $dataPagamento,
            'comprovante_path' => $comp_path ?? null,
            'end_to_end_id' => $retorno['endToEndId'],
            'status' => 'pago'
        ]);

        $mensagem = $retorno['mensagem'] ?? 'Pagamento efetuado com sucesso!';

        $this->fechar();
        $this->dispatch('toast-message', $mensagem);
    }

    /**
     * Não gera comprovante, job vai consultar instituição posteriormente
     */
    public function processarPagamentoEmAndamento(array $retorno){
        $solicitacaoService = new SolicitacoesPagamentoService;

        $dataPagamento = \Carbon\Carbon::parse($retorno['pagamento']['data_pagamento'])->format('Ymd');

        $solicitacao = $solicitacaoService->store([
            'tipo' => $this->tipo_pix == 'chave' ? 'pix' : 'pix_copia_cola',
            'identificador' => $this->identificador,
            'conta_id' => $this->selected_conta,
            'valor' => $retorno['pagamento']['valor'],
            'data_pagamento' => $dataPagamento,
            'end_to_end_id' => $retorno['endToEndId'],
            'status' => 'em_processamento'
        ]);

        $mensagem = $retorno['mensagem'] ?? 'Pagamento em etapa de processo na instituição bancária!';

        $this->fechar();
        $this->dispatch('toast-message', $mensagem);
    }
    
    public function render()
    {
        return view('livewire.modais.contas-pagar.pix');
    }
}
