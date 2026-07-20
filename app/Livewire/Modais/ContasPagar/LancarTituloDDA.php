<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Helpers\Empresa;

use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\EntidadeService;
use App\Services\FormaPagamentoService;
use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;
use App\Services\SolicitacoesPagamentoService;


class LancarTituloDDA extends Component
{
    /* Variáveis para o formulario */
    public $categoriasFinanceira, $centrosCusto, $entidades, $formasPagamento;
    
    /* Variáveis do formulário */
    public $entidade_id, $descricao, $valor_total, $data_emissao, $data_vencimento, $forma_pagamento_id;
    public $categoria_financeira_id, $centro_custo_id, $numero_nf, $observacoes;

    /* Variáveis do DDA */
    public array $dadosDDA;
    public $vencimentoDDA, $valorDDA, $nomeBeneficiario, $documentoBeneficiario, $linhaDigitavel;
    
    public array $nova_entidade = [];
    public function mount(
        CategoriaFinanceiraService $categoriaFinanceiraService,
        CentroCustoService $centroCustoService,
        EntidadeService $entidadeService,
        FormaPagamentoService $formaPagamentoService,
        array $dadosDDA
    ){
        $this->categoriasFinanceira = $categoriaFinanceiraService->showDespesas();
        $this->centrosCusto = $centroCustoService->show();
        $this->entidades = $entidadeService->showFornecedores();
        $this->formasPagamento = $formaPagamentoService->show();
        $this->dadosDDA = $dadosDDA;
        $this->valorDDA = $dadosDDA['valor'];
        $this->vencimentoDDA = $dadosDDA['vencimento']->toDateString();
        $this->data_vencimento = $this->vencimentoDDA ?? null;
        $this->valor_total = $this->valorDDA ?? null;
        $this->nomeBeneficiario = $dadosDDA['nome_beneficiario'];
        $this->documentoBeneficiario = $dadosDDA['documento_beneficiario'];
        $this->linhaDigitavel = $dadosDDA['linha_digitavel'];

        $somenteNumeros = preg_replace('/[^0-9]/', '', $this->documentoBeneficiario);
        if (strlen($somenteNumeros) === 11) {
            $cpfCnpjFormatado = preg_replace(
                '/(\d{3})(\d{3})(\d{3})(\d{2})/',
                '$1.$2.$3-$4',
                $somenteNumeros
            );
        } elseif (strlen($somenteNumeros) === 14) {
            $cpfCnpjFormatado = preg_replace(
                '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
                '$1.$2.$3/$4-$5',
                $somenteNumeros
            );
        } else {
            $cpfCnpjFormatado = $this->documentoBeneficiario;
        }

        $this->nova_entidade = [
            'razao_social' => $this->nomeBeneficiario,
            'nome_fantasia' => $this->nomeBeneficiario,
            'cpf_cnpj' => $cpfCnpjFormatado,
            'tipo' => strlen($somenteNumeros) > 11 ? 'PJ' : 'PF',
            'classificacao' => 'fornecedor',
            'dia_vencimento_padrao' => null
        ];
    }

    public function rules(){
        return [
            'entidade_id' => 'required|exists:entidade,id',
            'categoria_financeira_id' => 'nullable|exists:categoria_financeira,id',
            'centro_custo_id' => 'nullable|exists:centro_custo,id',
            'numero_nf' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|min:2|max:5000',
            'descricao' => 'required|string|min:2|max:255',
            'valor_total' => [
                'required',
                'numeric',
                'in:' . $this->valorDDA,
            ],
            'data_emissao' => 'nullable|date',
            'data_vencimento' => [
                'required',
                'date',
                'in:' . $this->vencimentoDDA
            ]
        ];
    }

    public function messages(){
        return [
            'entidade_id.required' => 'A entidade é obrigatória.',
            'entidade_id.exists' => 'A entidade informada é inválida.',

            'categoria_financeira_id.exists' => 'A categoria financeira informada é inválida.',

            'centro_custo_id.exists' => 'O centro de custo informado é inválido.',

            'numero_nf.string' => 'O número da nota fiscal deve ser um texto.',
            'numero_nf.max' => 'O número da nota fiscal não pode ter mais que :max caracteres.',

            'observacoes.string' => 'As observações devem ser um texto.',
            'observacoes.min' => 'As observações devem ter pelo menos :min caracteres.',
            'observacoes.max' => 'As observações não podem ter mais que :max caracteres.',

            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.min' => 'A descrição deve ter pelo menos :min caracteres.',
            'descricao.max' => 'A descrição não pode ter mais que :max caracteres.',

            'valor_total.required' => 'O valor total é obrigatório.',
            'valor_total.numeric' => 'O valor total deve ser um número.',
            'valor_total.min' => 'O valor total não pode ser negativo.',
            'valor_total.in' => 'O valor informado deve ser exatamente o valor do DDA.',

            'data_emissao.date' => 'A data de emissão deve ser uma data válida.',

            'data_vencimento.required' => 'A data de vencimento é obrigatória.',
            'data_vencimento.date' => 'A data de vencimento deve ser uma data válida.',
            'data_vencimento.date_format' => 'A data de vencimento deve estar no formato YYYY-MM-DD.', 
            'data_vencimento.in' => 'A data de vencimento deve ser igual a do boleto no DDA.'
        ];
    }

    public function salvarEntidadeRapido(EntidadeService $entidadeService)
    {
        $this->validate(
            [
                'nova_entidade.razao_social' => 'required|string',
                'nova_entidade.cpf_cnpj' => [
                    'required',
                    'string',
                    'max:18',
                    Rule::unique('entidade', 'cpf_cnpj')->where(fn ($q) => $q->where('empresa_parametro_id', Empresa::id()))
                ],
                'nova_entidade.tipo' => 'required|in:PF,PJ',
                'nova_entidade.classificacao' => 'required|string',
            ],
            [
                'nova_entidade.razao_social.required' => 'Informe a razão social.',
                'nova_entidade.cpf_cnpj.required' => 'Informe o CPF/CNPJ.',
                'nova_entidade.cpf_cnpj.max' => 'O CPF/CNPJ deve ter no máximo 18 caracteres.',
                'nova_entidade.cpf_cnpj.unique' => 'Já existe uma entidade cadastrada com este CPF/CNPJ.',
                'nova_entidade.tipo.required' => 'Selecione o tipo da entidade.',
                'nova_entidade.tipo.in' => 'O tipo informado é inválido.',
                'nova_entidade.classificacao.required' => 'Selecione a classificação da entidade.',
            ]
        );

        $entidade = $entidadeService->store($this->nova_entidade); 
        $this->entidades = $entidadeService->showFornecedores();
        $this->entidade_id = $entidade->id;
        $this->dispatch('entidade-cadastrada');
    }

    public function submit(TituloFinanceiroService $tituloService, ParcelaService $parcelaService, SolicitacoesPagamentoService $solicitacaoService){
        try{
            $data = $this->validate();

            DB::beginTransaction();

            $tituloData = [
                    'centro_custo_id' => $data['centro_custo_id'] ?? null,
                    'categoria_financeira_id' => $data['categoria_financeira_id'] ?? null,
                    'entidade_id' => $data['entidade_id'],
                    'descricao' => $data['descricao'],
                    'observacoes' => $data['observacoes'] ?? null,
                    'numero_nf' => $data['numero_nf'] ?? null,
                    'valor_total' => $data['valor_total'],
                    'data_emissao' => $data['data_emissao'] ?? Carbon::today(),
                    'tipo' => 'pagar',
                    'status' => 'ativo',
                ];
            $titulo = $tituloService->store($tituloData);

            $parcela = $parcelaService->store([
                'titulo_financeiro_id' => $titulo->id,
                'numero_parcela' => 1, # parcela unica
                'valor' => $titulo->valor_total,
                'data_vencimento' => $data['data_vencimento'],
                'status' => 'ativo'
            ]);

            $solicitacaoService->store([
                'parcela_id' => $parcela->id,
                'tipo' => 'codigo_barras',
                'identificador' => $this->linhaDigitavel,
                'valor' => $this->valorDDA,
            ]);

            DB::commit();
            $this->dispatch('toast-message', 'Título, parcela e solicitação de pagamento criado com sucesso!');
            $this->fechar();
        }catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        }catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast-error', 'Erro ao salvar título financeiro.');
            \Log::error("Erro ao salvar título: ", ['erro' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    public function fechar(){
        $this->dispatch('fechar-modal-cadastro-despesa');
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.lancar-titulo-d-d-a');
    }
}
