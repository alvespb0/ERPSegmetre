<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Services\BoletoCobrancaService;

use App\Models\Parcela;
use App\Models\Conta;
use App\Models\BoletoCobranca;

class GerarCobrancaLote extends Component
{
    public array $parcelasIds = [];
    public $parcelas;
    public $contas;
    public $selectedConta;
    public $configuracoes;
    public $pagador;

    public $tipoIntegracao;
    public $modalidade = 1;
    public $especie_documento = 'DM';
    public $codigo_juros;
    public $valor_juros;
    public $dias_inicio_juros;
    public $codigo_multa;
    public $valor_multa;
    public $dias_inicio_multa;
    public $dias_limite_pagamento;
    public $info_complementares = [];

    public function mount(array $parcelasIds){
        $this->parcelasIds = $parcelasIds;
        $this->parcelas = Parcela::with(['titulo.entidade'])->whereIn('id', $this->parcelasIds)->get();
        $this->contas = Conta::orderBy('nome', 'asc')->get();
    }

    /**
     * Dispara um evento para o front-end indicando o fechamento do modal de cobrança.
     *
     * @return void
     */
    public function fecharModal(){
        $this->dispatch('fechar-modal-cobranca-lote');
    }

    /**
     * Seleciona uma conta para cobrança e preenche automaticamente as configurações atreladas a ela.
     *
     * @param Conta $conta A instância da conta bancária selecionada.
     * @return void
     */
    public function selectContaCobranca(Conta $conta){
        $conta->load([
            'banco',
            'configuracaoCobranca'
        ]);

        $this->selectedConta = $conta;
        $this->configuracoes = $conta->configuracaoCobranca ?? null;
        $this->codigo_juros = $this->configuracoes->codigo_juros ?? '';
        $this->valor_juros = $this->configuracoes->valor_juros ?? '';
        $this->codigo_multa = $this->configuracoes->codigo_multa ?? '';
        $this->valor_multa = $this->configuracoes->valor_multa ?? '';
        $this->dias_inicio_juros = $this->configuracoes->dias_inicio_juros ?? '';
        $this->dias_inicio_multa = $this->configuracoes->dias_inicio_multa ?? '';
        $this->dias_limite_pagamento = $this->configuracoes->dias_limite_pagamento ?? '';
        
        $integracao = $this->configuracoes->integracao ?? null;
        $this->tipoIntegracao = $integracao
            ? 'api'
            : 'remessa';
    }

    /**
     * Limpa as informações da conta de cobrança previamente selecionada.
     *
     * @return void
     */
    public function limparContaCobranca(){
        $this->selectedConta = null;
        $this->configuracao = null;
    }  

    /**
     * Define as regras de validação para as propriedades do componente.
     *
     * @return array<string, string> Retorna o array contendo as regras.
     */
    public function rules(){
        return [
            'especie_documento' => 'required|in:CH,DM,DMI,DS,DSI,DR,LC,NCC,NCE,NCI,NCR,NP,NPR,TM,TS,NS,RC,FAT,ND,AP,ME,PC,NF,DD,BDP,OU',
            'modalidade' => 'required|in:1,2,3,4,5,outro',
            'info_complementares' => 'nullable|array|max:40',
            'info_complementares.*' => 'nullable|string|max:100',
            'dias_limite_pagamento' => 'required|integer|min:0',
            'codigo_juros' => 'required|in:0,1,2',
            'valor_juros' => 'required_unless:codigo_juros,0|numeric',
            'dias_inicio_juros' => 'required_unless:codigo_juros,0|integer|min:0|lte:dias_limite_pagamento',
            'codigo_multa' => 'required|in:0,1,2',
            'valor_multa' => 'required_unless:codigo_multa,0|numeric',
            'dias_inicio_multa' => 'required_unless:codigo_juros,0|integer|min:0|lte:dias_limite_pagamento',
        ];
    }

    /**
     * Define as mensagens customizadas para os erros de validação das regras.
     *
     * @return array<string, string> Retorna o array de mensagens de erro.
     */
    public function messages(){
        return [
            'especie_documento.required' => 'A espécie do documento é obrigatória.',
            'especie_documento.in' => 'A espécie do documento informada é inválida.',

            'modalidade.required' => 'A modalidade de cobrança é obrigatória.',
            'modalidade.in' => 'A modalidade de cobrança informada é inválida.',

            'info_complementares.array' => 'As informações complementares precisam ser uma lista de textos.',
            'info_complementares.max' => 'É permitido informar no máximo 40 informações complementares.',

            'info_complementares.*.string' => 'Cada informação complementar deve ser um texto.',
            'info_complementares.*.max' => 'Cada informação complementar não pode ultrapassar 100 caracteres.',

            'dias_limite_pagamento.required' => 'Informe o prazo limite para pagamento do boleto.',
            'dias_limite_pagamento.integer' => 'O prazo limite para pagamento deve ser um número inteiro.',
            'dias_limite_pagamento.min' => 'O prazo limite para pagamento não pode ser negativo.',

            'codigo_juros.required' => 'Informe o tipo de juros.',
            'codigo_juros.in' => 'O tipo de juros informado é inválido.',

            'valor_juros.required_unless' => 'Informe o valor dos juros.',
            'valor_juros.numeric' => 'O valor dos juros deve ser numérico.',

            'dias_inicio_juros.required_unless' => 'Informe em quantos dias após o vencimento os juros serão aplicados.',
            'dias_inicio_juros.integer' => 'Os dias para início dos juros devem ser um número inteiro.',
            'dias_inicio_juros.min' => 'Os dias para início dos juros não podem ser negativos.',
            'dias_inicio_juros.lte' => 'Os dias para início dos juros não podem ser maiores que o prazo limite para pagamento.',

            'codigo_multa.required' => 'Informe o tipo de multa.',
            'codigo_multa.in' => 'O tipo de multa informado é inválido.',

            'valor_multa.required_unless' => 'Informe o valor da multa.',
            'valor_multa.numeric' => 'O valor da multa deve ser numérico.',

            'dias_inicio_multa.required_unless' => 'Informe em quantos dias após o vencimento a multa será aplicada.',
            'dias_inicio_multa.integer' => 'Os dias para início da multa devem ser um número inteiro.',
            'dias_inicio_multa.min' => 'Os dias para início da multa não podem ser negativos.',
            'dias_inicio_multa.lte' => 'Os dias para início da multa não podem ser maiores que o prazo limite para pagamento.',
        ];
    }

    /**
     * Realiza verificações de regra de negócio para a emissão do boleto.
     * Valida vencimento, status da parcela, existência da conta e chama a validação do pagador.
     *
     * @throws ValidationException Caso alguma das condições não seja atendida.
     * @return void
     */
    private function validarEmissaoBoleto(): void{
        foreach($this->parcelas as $parcela){
            if (Carbon::parse($parcela->data_vencimento)->isPast()) {
                throw ValidationException::withMessages([
                    'geral' => 'Não é possível emitir boleto. Parcela nº ' . $parcela->numero_parcela . ' está com data retroativa à hoje.'
                ]);
            }

            if ($parcela->status == 'cancelada') {
                throw ValidationException::withMessages([
                    'geral' => 'Não é possível emitir boleto. Parcela nº '. $parcela->numero_parcela . ' está com status CANCELADA.'
                ]);
            }

            if($parcela->possui_boleto_ativo){
                throw ValidationException::withMessages([
                    'geral' => 'Não é possível emitir boleto. Parcela nº ' . $parcela->numero_parcela . ' Já possui um boleto ativo para esse titulo.'
                ]);
            }
        }

        if (!$this->selectedConta) {
            throw ValidationException::withMessages([
                'geral' => 'Selecione uma conta para cobrança.'
            ]);
        }

        if (!$this->configuracoes) {
            throw ValidationException::withMessages([
                'geral' => 'A conta selecionada não possui configuração de cobrança.'
            ]);
        }

        $this->validarJuros();
        $this->validarMulta();
        $this->validarPagador();
    }

    /**
     * Valida as configurações de multa do boleto.
     *
     * Regras Sicoob:
     *
     * - Multa percentual não pode resultar em valor superior a R$ 1,00.
     * - Multa por valor fixo não pode ultrapassar R$ 1,00 considerando
     *   um período mensal de referência (30 dias).
     *
     * @throws ValidationException
     *
     * @return void
     */
    public function validarMulta(){
        foreach($this->parcelas as $parcela){
            $valorParcela = $parcela->valor;
            if ($this->codigo_multa == 2) {
                $valorCalculado = $valorParcela * ($this->valor_multa / 100);
                if ($valorCalculado > 1) {
                    throw ValidationException::withMessages([
                        'geral' => 'Não é possível emitir boleto. O valor mensal de multa não pode ultrapassar R$ 1,00.'
                    ]);
                }
            }

            if ($this->codigo_multa == 1) {
                $valorCalculado = $this->valor_multa * 30;
                if ($valorCalculado > 1) {
                    throw ValidationException::withMessages([
                        'geral' => 'Não é possível emitir boleto. O valor mensal de multa não pode ultrapassar R$ 1,00.'
                    ]);
                }
            }
        }
    }

    /**
     * Valida as configurações de juros do boleto.
     *
     * Regras Sicoob:
     *
     * - Juros percentual não pode resultar em valor superior a R$ 1,00.
     * - Juros por valor fixo não pode ultrapassar R$ 1,00 considerando
     *   um período mensal de referência (30 dias).
     *
     * @throws ValidationException
     *
     * @return void
     */
    public function validarJuros(){
        foreach($this->parcelas as $parcela){
            $valorParcela = $parcela->valor;
            if ($this->codigo_juros == 2) {
                $valorCalculado = $valorParcela * ($this->valor_juros / 100);
                if ($valorCalculado > 1) {
                    throw ValidationException::withMessages([
                        'geral' => 'Não é possível emitir boleto. O valor mensal de juros não pode ultrapassar R$ 1,00.'
                    ]);
                }
            }

            if ($this->codigo_juros == 1) {
                $valorCalculado = $this->valor_juros * 30;
                if ($valorCalculado > 1) {
                    throw ValidationException::withMessages([
                        'geral' => 'Não é possível emitir boleto. O valor mensal de juros não pode ultrapassar R$ 1,00.'
                    ]);
                }
            }
        }
    }

    /**
     * Verifica se todas as parcelas são do mesmo pagador
     * Verifica se o pagador possui todas as informações essenciais cadastradas (Documento e Endereço).
     *
     * @throws ValidationException Caso faltem dados obrigatórios no cadastro do pagador.
     * @return void
     */
    public function validarPagador(){
        $entidades = $this->parcelas
            ->pluck('titulo.entidade.id')
            ->filter()
            ->unique();

        if ($entidades->count() > 1) {
            throw ValidationException::withMessages([
                'geral' => 'Todas as parcelas devem pertencer à mesma entidade.',
            ]);
        }
        
        $pagador = $this->parcelas->first()->titulo->entidade;

        $endereco = $pagador->enderecos()?->first();

        $errosCadastro = array_filter([
            'Razão Social/Nome' => empty($pagador->razao_social) && empty($pagador->nome_fantasia),
            'CPF/CNPJ válido' => empty($pagador->cpf_cnpj),
            'CEP do endereço' => empty($endereco?->cep),
            'Estado (UF)' => empty($endereco?->uf),
            'Cidade' => empty($endereco?->cidade),
            'Bairro' => empty($endereco?->bairro),
            'Rua / Logradouro' => empty($endereco?->rua)
        ]);

        if (!empty($errosCadastro)) {
            $camposFaltantes = implode(', ', array_keys($errosCadastro));
            
            throw ValidationException::withMessages([
                'geral' => "Para gerar o boleto, preencha os seguintes dados do cliente: {$camposFaltantes}."
            ]);
        }
    }

        /**
     * Processa a geração da cobrança ou preparação de remessa.
     *
     * Valida as regras de negócio e informações do formulário, cria o registro do 
     * boleto no banco de dados e, dependendo do tipo de integração (API ou Remessa), 
     * envia a requisição ao provedor bancário ou deixa pendente para o arquivo de remessa.
     *
     * @throws ValidationException Se as validações falharem ou a comunicação com o banco retornar erro.
     * @throws \Exception Para erros internos e não previstos, acionando o Rollback da transação.
     * @return void
     */
    public function gerar(){
        try{
            $this->validate(); # Valida form query
            $this->validarEmissaoBoleto(); # Valida dados de emissao de boleto

            if($this->tipoIntegracao == 'api'){ # factory e service provider vai ser a mesma para todas as cobrancas
                $factory = new \App\Factories\IntegracaoFactory;
                $serviceProvider = $factory->make($this->configuracoes->integracao, 'cobranca');
            }

            $erros = []; # instancia uma array de erros para caso tenha um erro de cadastro de cobranca
            $sucessos = []; # instancia uma array de sucesso de cadastro de cobranca

            foreach($this->parcelas as $parcela){ # itera parcela para cadastrar os boletos por parcela
                DB::beginTransaction(); # inicia transacao, se der erro na api para geracao do boleto rollback no banco

                try {

                $boletoCrud = new BoletoCobrancaService;
                
                $sequencial = BoletoCobranca::proximoSequencial();

                $boleto = $boletoCrud->store([
                    'parcela_id' => $parcela->id,
                    'configuracao_cobranca_id' => $this->configuracoes->id,

                    'sequencial_boleto' => $sequencial,
                    'numero_documento' => BoletoCobranca::gerarNumeroDocumento($sequencial),

                    'modalidade' => $this->modalidade,
                    'info_complementares' => $this->info_complementares[$parcela->id] ?? null,
                    'especie_documento' => $this->especie_documento,

                    'codigo_multa' => $this->codigo_multa,
                    'codigo_juros' => $this->codigo_juros,

                    'valor_multa' => $this->valor_multa,
                    'valor_juros' => $this->valor_juros,

                    'data_multa' => Carbon::parse($parcela->data_vencimento)->copy()->addDays($this->dias_inicio_multa),
                    'data_juro' => Carbon::parse($parcela->data_vencimento)->copy()->addDays($this->dias_inicio_juros),
                ]);

                if($this->tipoIntegracao != 'api'){
                    $boleto->update([
                        'status' => 'pendente_remessa'
                    ]);

                    DB::commit();

                    $sucessos[] = [
                        'parcela' => $parcela->numero_parcela,
                        'gerado' => true
                    ];

                    continue;
                }

                if($this->configuracoes->ambiente == 'homologacao'){
                    $resultado = $serviceProvider->gerarBoletoSandbox($boleto);
                }else{
                    $resultado = $serviceProvider->gerarBoletoProducao($boleto);
                }

                $boleto->update([
                    'status' => $resultado['status'],
                    'nosso_numero' => $resultado['nosso_numero'],
                    'linha_digitavel' => $resultado['linha_digitavel'],
                    'codigo_barras' => $resultado['codigo_barras'],
                    'pdf_path' => $resultado['pdf_path'],
                ]);

                DB::commit(); # se tudo deu certo ele commita, se der errado os services vao disparar exception cai no throwable e da rollback nessa transacao
                $sucessos[] = [
                    'parcela' => $parcela->numero_parcela,
                ];
            } catch (\Throwable $e) {
                DB::rollBack();
                \Log::error([
                    'Erro ao gerar boleto' => $e->getMessage(),
                    'Parcela' => $parcela->id
                ]);
                $erros[] = [
                    'parcela' => $parcela->numero_parcela,
                    'erro' => $e->getMessage(),
                ];
            }
        }
        
        if(count($erros) > 0){
            foreach($erros as $erro){
                $this->dispatch('toast-error', 'Não foi possivel gerar a cobranca para a parcela nº '. $erro['parcela']);
            }
        }

        if(count($sucessos) > 0){
            foreach($sucessos as $sucesso){
                $this->dispatch('toast-message', 'Cobranca gerada para a parcela nº' . $sucesso['parcela']);
            }
        }

        $this->dispatch('fechar-modal-cobranca-lote');
        } catch (ValidationException $e) {
            throw $e; 
        } catch(\Throwable $e){
            \Log::error([
                    'Erro ao gerar as cobranças' => $e->getMessage(),
                ]);
            $this->dispatch('toast-error', 'Erro ao gerar as cobranças.');
            $this->dispatch('fechar-modal-cobranca-lote');
        }
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.gerar-cobranca-lote');
    }
}
