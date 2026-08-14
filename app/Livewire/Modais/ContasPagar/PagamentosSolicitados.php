<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use Illuminate\Support\Facades\Storage;

use App\Jobs\ConsultaComprovanteJob;

use App\Helpers\Empresa;

use App\Models\SolicitacoesPagamento;
use App\Models\Conta;
use App\Models\Movimentacao;

use App\Services\MovimentacaoService;
class PagamentosSolicitados extends Component
{
    public $solicitacao;
    public $contas;
    public $selected_conta;
    public $saldo, $limite, $bloqueado;
    public $consultaDespesaRet = [];

    /**
     * Inicializa o componente carregando a solicitação de pagamento
     * e as contas bancárias disponíveis.
     *
     * @param int $solicitacaoId ID da solicitação de pagamento.
     * @return void
     */
    public function mount($solicitacaoId){
        $this->solicitacao = SolicitacoesPagamento::findOrFail($solicitacaoId);
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
                'consultaDespesaRet',
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

    public function consultaDespesa(){
        try{
            
            if(!$this->selected_conta){
                $this->dispatch('toast-error', 'Selecione uma conta de origem primeiro.');
            }

            $conta = Conta::findOrFail($this->selected_conta);

            $config = $conta->configuracaoCobranca;

            if(!$config->integracao){
                $this->dispatch('toast-error', 'A conta de origem não possui integração bancária para completar a transação.');
                return;
            }
            
            switch($this->solicitacao->tipo){
                case 'codigo_barras':
                    $this->consultaDespesaRet = $this->consultaBoletoIntegracao($config, $conta);
                    return;
                case 'pix':
                    $this->consultaDespesaRet = $this->consultaPixIntegracao($config, $conta);
                    return;
                case 'pix_copia_cola':
                    # ...
                case 'tributo':
                    # ... 
            }
            
        } catch(\Throwable $e){
            \Log::error([
                'Erro ao buscar consultar despesa para pagamento' => $e->getMessage(),
                'Conta' => $this->selected_conta,
                'Empresa Parâmetro' => Empresa::id()
            ]);

            $message = 'Erro ao consultar despesa.';

            if (method_exists($e, 'friendlyMessage')) {
                $message = $e->friendlyMessage();
            }

            $this->dispatch('toast-error', $message);
        }
    }

    public function consultaBoletoIntegracao($config, Conta $conta){
        $integracao = $config->integracao;        
        $factory = new \App\Factories\IntegracaoFactory;
        $serviceProvider = $factory->make($integracao, 'pagamento');

        if ($config->ambiente === 'homologacao') {

            if (!method_exists($serviceProvider, 'consultaBoletoPagamentoSandbox')) {
                $this->dispatch('toast-error', 'Integração não implementa consulta de boleto SANDBOX.');
                return;
            }

            return $serviceProvider->consultaBoletoPagamentoSandbox($conta, $this->solicitacao->identificador);
        } elseif ($config->ambiente === 'producao') {

            if (!method_exists($serviceProvider, 'consultaBoletoPagamentoProducao')) {
                $this->dispatch('toast-error', 'Integração não implementa consulta de boleto.');
                return;
            }

            return $serviceProvider->consultaBoletoPagamentoProducao($conta, $this->solicitacao->identificador);
        }       
    }

    public function consultaPixIntegracao($config, Conta $conta){
        $integracao = $config->integracao;        
        $factory = new \App\Factories\IntegracaoFactory;
        $serviceProvider = $factory->make($integracao, 'pix_pagamento');

        if ($config->ambiente === 'homologacao') {

            if (!method_exists($serviceProvider, 'iniciaPixSandbox')) {
                $this->dispatch('toast-error', 'Integração não implementa pix sandbox.');
                return;
            }

            return $serviceProvider->iniciaPixSandbox($this->solicitacao->identificador);
        } else if ($config->ambiente === 'producao') {

            if (!method_exists($serviceProvider, 'iniciaPixProducao')) {
                $this->dispatch('toast-error', 'Integração não implementa pix.');
                return;
            }

            return $serviceProvider->iniciaPixProducao($this->solicitacao->identificador);
        }       
    }

    public function processarPagamento(){
        try{
            if(!$this->consultaDespesaRet){
                $this->dispatch('toast-error', 'Realize uma nova consutla de despesa.');
                return;
            }

            $conta = Conta::findOrFail($this->selected_conta);

            $config = $conta->configuracaoCobranca;

            if(!$config->integracao){
                $this->dispatch('toast-error', 'A conta de origem não possui integração bancária para completar a transação.');
                return;
            }

            $retorno = [];

            if ($config->ambiente === 'homologacao') {

                if($this->solicitacao->tipo == 'codigo_barras'){

                    $integracao = $config->integracao;        
                    $factory = new \App\Factories\IntegracaoFactory;
                    $serviceProvider = $factory->make($integracao, 'pagamento');

                    if (!method_exists($serviceProvider, 'processarPagamentoSandbox')) {
                        $this->dispatch('toast-error', 'Integração não implementa pagamento de boleto SANDBOX.');
                        return;
                    }

                    $retorno = $serviceProvider->processarPagamentoSandbox($conta, $this->solicitacao->identificador, $this->consultaDespesaRet);

                }else if($this->solicitacao->tipo == 'pix'){
                    
                    $integracao = $config->integracao;        
                    $factory = new \App\Factories\IntegracaoFactory;
                    $serviceProvider = $factory->make($integracao, 'pix_pagamento');

                    if (!method_exists($serviceProvider, 'confirmaPixSandbox')) {
                        $this->dispatch('toast-error', 'Integração não implementa pix sandbox.');
                        return;
                    }

                    $retorno = $serviceProvider->confirmaPixSandbox();

                }

            } elseif ($config->ambiente === 'producao') {

                if($this->solicitacao->tipo == 'codigo_barras'){

                    $integracao = $config->integracao;        
                    $factory = new \App\Factories\IntegracaoFactory;
                    $serviceProvider = $factory->make($integracao, 'pagamento');

                    if (!method_exists($serviceProvider, 'processarPagamentoProducao')) {
                        $this->dispatch('toast-error', 'Integração não implementa pagamento de boleto.');
                        return;
                    }
                    
                    $retorno = $serviceProvider->processarPagamentoProducao($conta, $this->solicitacao->identificador, $this->consultaDespesaRet);
                    
                }else if($this->solicitacao->tipo == 'pix'){

                    $integracao = $config->integracao;        
                    $factory = new \App\Factories\IntegracaoFactory;
                    $serviceProvider = $factory->make($integracao, 'pix_pagamento');

                    if (!method_exists($serviceProvider, 'confirmaPixProducao')) {
                        $this->dispatch('toast-error', 'Integração não implementa pix.');
                        return;
                    }

                    $retorno = $serviceProvider->confirmaPixProducao($this->consultaDespesaRet['e2eId'], $this->solicitacao->valor, 'chave');
                }
            }       
            

            \Log::debug([
                'Retorno da transacao de pagamento' => $retorno
            ]);

            match ($retorno['status']) {
                'pago' => $this->processarPagamentoEfetivado($retorno, $conta),
                'agendado' => $this->processarPagamentoAgendado($retorno, $conta),
                'pendente_assinatura' => $this->processarPagamentoPendente($retorno, $conta),
                'processado' => $this->processarPagamentoProcessado($retorno, $conta),
                'em_processamento' => $this->processarPagamentoEmAndamento($retorno, $conta),
                'rejeitado' || 'recusado' => throw new \Exception(
                    $retorno['mensagem'] ?? 'Pagamento rejeitado.'
                ),
                default => throw new \RuntimeException('Status de pagamento desconhecido.')
            };
            
        } catch(\Throwable $e){
            \Log::error([
                'Erro ao buscar tentar realizar transação para pagamento de despesa' => $e->getMessage(),
                'Conta' => $this->selected_conta,
                'Empresa Parâmetro' => Empresa::id()
            ]);

            $message = 'Erro ao processar pagamento.';

            if (method_exists($e, 'friendlyMessage')) {
                $message = $e->friendlyMessage();
            }

            $this->dispatch('toast-error', $message);
        }

    }

    private function processarPagamentoEfetivado(array $retorno, $conta){
        \Log::info([
            'Iniciado Lancamento de Movimentacao' => [
                'solicitacao' => $this->solicitacao->id,
                'valor' => $retorno['pagamento']['valor'],
                'data_pagamento' => $retorno['pagamento']['data_pagamento'],
                'empresa_parametro_id' => Empresa::id(),
            ]
        ]);

        $serviceMovimentacao = new MovimentacaoService;

        $movimentacao = $serviceMovimentacao->store([
            'conta_id' => $conta->id,
            'empresa_parametro_id' => Empresa::id(),
            'parcela_id' => $this->solicitacao->parcela_id,
            'valor_pago' => $retorno['pagamento']['valor'] ?? $this->solicitacao->valor,
            'data_pagamento' => $retorno['pagamento']['data_pagamento'] ?? Carbon::now(),
        ]);
        
        $service = new \App\Services\SolicitacoesPagamentoService;

        $compPath = $service->makeComprovantePagamento($retorno);

        if($this->solicitacao->tipo == 'codigo_barras'){

            $this->solicitacao->update([
                'movimentacao_id' => $movimentacao->id,
                'conta_id' => $conta->id,
                'chave_idempotente' => $retorno['idempotency_key'],
                'data_pagamento' => $retorno['pagamento']['data_pagamento'],
                'codigo_autenticacao' => $retorno['pagamento']['codigo_autenticacao'] ?? null,
                'id_pagamento' => $retorno['pagamento']['id_pagamento'] ?? null,
                'comprovante_path' => $compPath ?? null,
                'status' => 'pago'
            ]);

            $mensagem = $retorno['mensagem'] ?? 'Pagamento efetuado com sucesso!';

            $this->fechar();
            $this->dispatch('toast-message', $mensagem);
        }else if($this->solicitacao->tipo == 'pix_copia_cola'){
            // falta a logica ainda
        }else if($this->solicitacao->tipo == 'pix'){
            $this->solicitacao->update([
                'movimentacao_id' => $movimentacao->id,
                'conta_id' => $conta->id,
                'end_to_end_id' => $retorno['endToEndId'],
                'data_pagamento' => $retorno['pagamento']['data_pagamento'] ?? Carbon::now(),
                'comprovante_path' => $compPath ?? null,
                'status' => 'pago'
            ]);

            $mensagem = $retorno['mensagem'] ?? 'Pagamento efetuado com sucesso!';

            $this->fechar();
            $this->dispatch('toast-message', $mensagem);
        }
    }

    private function processarPagamentoAgendado(array $retorno, $conta){
        if($this->solicitacao->tipo == 'codigo_barras'){
            $this->solicitacao->update([
                'chave_idempotente' => $retorno['idempotency_key'],
                'data_pagamento' => $retorno['pagamento']['data_pagamento'] ?? null,
                'conta_id' => $conta->id ?? null,
                'codigo_autenticacao' => $retorno['pagamento']['codigo_autenticacao'] ?? null,
                'id_pagamento' => $retorno['pagamento']['id_pagamento'] ?? null,
                'comprovante_path' => $compPath ?? null,
                'status' => 'agendado'
            ]);

            $mensagem = $retorno['mensagem'] ?? 'Pagamento agendado, comprovante será gerado após confirmação do banco.';

            $this->fechar();
            $this->dispatch('toast-message', $mensagem);
        }else if($this->solicitacao->tipo == 'pix_copia_cola'){
            // falta a logica ainda
        }else if($this->solicitacao->tipo == 'pix'){
            // falta a logica ainda 
        }

    }

    private function processarPagamentoPendente(array $retorno, $conta){
        if($this->solicitacao->tipo == 'codigo_barras'){
            $this->solicitacao->update([
                'chave_idempotente' => $retorno['idempotency_key'],
                'conta_id' => $conta->id ?? null,
                'status' => 'pendente_assinatura'
            ]);

            $this->fechar();
            $this->dispatch('toast-message', 'Pagamento pendente de aceite no app da instituição bancária.');
        }else if($this->solicitacao->tipo == 'pix_copia_cola'){
            // falta a logica ainda
        }else if($this->solicitacao->tipo == 'pix'){
            // falta a logica ainda 
        }
    }

    private function processarPagamentoProcessado(array $retorno, $conta){
        if($this->solicitacao->tipo == 'codigo_barras'){
            $this->solicitacao->update([
                'chave_idempotente' => $retorno['idempotency_key'],
                'conta_id' => $conta->id ?? null,
                'status' => 'pago'
            ]);

            $this->fechar();
            $this->dispatch('toast-message', 'Pagamento processado.');
            $this->dispatch('toast-message', 'Comprovante de pagamento não gerado, aguardando retorno da instituição bancária');
        }else if($this->solicitacao->tipo == 'pix_copia_cola'){
            // falta a logica ainda
        }else if($this->solicitacao->tipo == 'pix'){
            // falta a logica ainda 
        }
    }

    /**
     * Não gera comprovante, job vai consultar instituição posteriormente
     */
    private function processarPagamentoEmAndamento(array $retorno, Conta $conta){
        if($this->solicitacao->tipo == 'pix'){
            $dataPagamento = Carbon::parse($retorno['pagamento']['data_pagamento'])->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
            $this->solicitacao->update([
                'conta_id' => $conta->id,
                'valor' => $retorno['pagamento']['valor'],
                'data_pagamento' => $dataPagamento,
                'end_to_end_id' => $retorno['endToEndId'],
                'status' => 'em_processamento'
            ]);

            $mensagem = $retorno['mensagem'] ?? 'Pagamento em etapa de processo na instituição bancária!';

            $this->fechar();
            $this->dispatch('toast-message', $mensagem);
            ConsultaComprovanteJob::dispatch($this->solicitacao->id)->delay(now()->addSeconds(10));
        }else if($this->solicitacao->tipo == 'pix_copia_cola'){
            // falta a logica ainda
        }else if($this->solicitacao->tipo == 'codigo_barras'){
            // falta a logica ainda 
        }
    }

    public function downloadComprovante(){
        if(!$this->solicitacao->comprovante_path){
            $this->dispatch('toast-error', 'Nenhum comprovante de pagamento localizado.');
            return;
        }

        return Storage::disk('public')->download($this->solicitacao->comprovante_path);
    }

    public function fechar(){
        $this->dispatch('fechar-modal-pagamento-solicitacao');
    }

    /**
     * Renderiza a view do componente.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modais.contas-pagar.pagamentos-solicitados');
    }
}
