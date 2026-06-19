<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

use App\Services\BoletoCobrancaService;

use App\Models\Parcela;
use App\Models\Conta;
use App\Models\BoletoCobranca;

class GerarCobranca extends Component
{
    public $parcela;
    public $pagador;
    public $contas;
    public $selectedConta;
    public $configuracoes;
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
    public $info_complementares;

    /**
     * Inicializa o componente carregando os dados da parcela, do pagador e das contas disponíveis.
     *
     * @param int|string $parcelaId O ID da parcela que será cobrada.
     * @return void
     */
    public function mount($parcelaId){
        $this->parcela = Parcela::with(['titulo.entidade'])->findOrFail($parcelaId);
        $this->parcela->titulo->loadCount('parcelas');
        $this->pagador = $this->parcela->titulo->entidade;
        $this->contas = Conta::orderBy('nome', 'asc')->get();
    }

    /**
     * Dispara um evento para o front-end indicando o fechamento do modal de cobrança.
     *
     * @return void
     */
    public function fecharModal(){
        $this->dispatch('fechar-modal-cobranca');
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
            'info_complementares' => 'nullable|string|max:100',
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

            'info_complementares.string' => 'As informações complementares devem ser um texto válido.',
            'info_complementares.max' => 'As informações complementares não podem ultrapassar 100 caracteres.',

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
        if (Carbon::parse($this->parcela->data_vencimento)->isPast()) {
            throw ValidationException::withMessages([
                'geral' => 'Não é possível emitir boleto para parcela vencida.'
            ]);
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

        if ($this->parcela->status == 'cancelada') {
            throw ValidationException::withMessages([
                'geral' => 'Não é possível emitir boleto para parcela cancelada.'
            ]);
        }

        $this->validarPagador();
    }

    /**
     * Verifica se o pagador possui todas as informações essenciais cadastradas (Documento e Endereço).
     *
     * @throws ValidationException Caso faltem dados obrigatórios no cadastro do pagador.
     * @return void
     */
    public function validarPagador(){
        $endereco = $this->pagador?->enderecos()?->first();
        $errosCadastro = array_filter([
            'Razão Social/Nome' => empty($this->pagador->razao_social) && empty($this->pagador->nome_fantasia),
            'CPF/CNPJ válido' => empty($this->pagador->cpf_cnpj),
            'CEP do endereço' => empty($endereco?->cep),
            'Estado (UF)' => empty($endereco?->uf),
            'Cidade' => empty($endereco?->cidade),
            'Bairro' => empty($endereco?->bairro),
            'Rua / Logradouro' => empty($endereco?->rua)
        ]);

        if (!empty($errosCadastro)) {
            throw ValidationException::withMessages(
                [
                    'geral' => implode(' ', $erros)
                ]
            );
        }
    }

    public function gerar(){
        try{
            $data = $this->validate();
            $this->validarEmissaoBoleto();

            $boletoCrud = new BoletoCobrancaService;
            
            $sequencial = BoletoCobranca::proximoSequencial();

            $boleto = $boletoCrud->store([
                'parcela_id' => $this->parcela->id,
                'configuracao_cobranca_id' => $this->configuracoes->id,

                'sequencial_boleto' => $sequencial,
                'numero_documento' => BoletoCobranca::gerarNumeroDocumento($sequencial),

                'modalidade' => $this->modalidade,
                'info_complementares' => $this->info_complementares,
                'especie_documento' => $this->especie_documento,

                'codigo_multa' => $this->codigo_multa,
                'codigo_juros' => $this->codigo_juros,

                'valor_multa' => $this->valor_multa,
                'valor_juros' => $this->valor_juros,

                'data_multa' => Carbon::parse($this->parcela->data_vencimento)->copy()->addDays($this->dias_inicio_multa),
                'data_juro' => Carbon::parse($this->parcela->data_vencimento)->copy()->addDays($this->dias_inicio_juros),
            ]);
            
            if($this->tipoIntegracao == 'api'){
                $factory = new \App\Factories\IntegracaoFactory;
                $serviceProvider = $factory->make($this->configuracoes->integracao, 'cobranca');
            }else{
                $serviceProvider = '\Bancos\Gerador\Remessa240Generica'; # por enquanto hardcodado eu vou fazer algo pra quando não tem integração vinculada
            }

            if($this->configuracoes->ambiente == 'homologacao'){
                $boletoGerado = $serviceProvider->gerarBoletoSandbox($boleto);
            }else{
                $boletoGerado = $serviceProvider->gerarBoletoProducao($boleto);
            }

            if($boletoGerado->ok()){
                $boletoGerado->json();
                $pdfPath = null;

                if (!empty($boletoGerado['resultado']['pdfBoleto'])) {
                    $pdfContent = base64_decode($boletoGerado['resultado']['pdfBoleto']);
                    $nomeArquivo = "boleto_{$boleto->numero_documento}.pdf";
                    Storage::disk('public')->put(
                        "boletos/{$nomeArquivo}",
                        $pdfContent
                    );
                    $pdfPath = "boletos/{$nomeArquivo}";
                }

                $retorno = $boletoCrud->update([
                    'status' => 'registrado',
                    'nosso_numero' => $boletoGerado['resultado']['nossoNumero'] ?? null,
                    'codigo_barras' => $boletoGerado['resultado']['codigoBarras'] ?? null,
                    'linha_digitavel' => $boletoGerado['resultado']['linhaDigitavel'] ?? null,
                    'pdf_path' => $pdfPath,
                ], $boleto->id);

                $this->dispatch('fechar-modal-cobranca');
                $this->dispatch('toast-message', 'Boleto registrado com sucesso!');
            }
        } catch (ValidationException $e) {
            throw $e; 
        } catch(\Exception $e){
            \Log::error([
                    'Erro ao gerar boleto' => $e->getMessage(),
                ]);
            return $this->dispatch('toast-error', 'Erro ao gerar boleto.');
        }
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.gerar-cobranca');
    }
}
