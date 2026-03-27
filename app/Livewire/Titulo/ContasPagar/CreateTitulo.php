<?php

namespace App\Livewire\Titulo\ContasPagar;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

use App\Services\ContaService;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\EntidadeService;
use App\Services\FormaPagamentoService;
use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;

class CreateTitulo extends Component
{
    public $entidade_id, $descricao, $valor_total, $data_emissao, $data_vencimento, $quantidade_parcelas;
    public $conta_id, $categoria_financeira_id, $centro_custo_id, $numero_nf, $observacoes;
    public $contas, $categoriasFinanceira, $centrosCusto, $entidades, $formasPagamento;
    public $parcelas = [];

    public function mount(ContaService $contaService,
        CategoriaFinanceiraService $categoriaFinanceiraService,
        CentroCustoService $centroCustoService,
        EntidadeService $entidadeService,
        FormaPagamentoService $formaPagamentoService
    ){
        $this->contas = $contaService->show();
        $this->categoriasFinanceira = $categoriaFinanceiraService->showDespesas();
        $this->centrosCusto = $centroCustoService->show();
        $this->entidades = $entidadeService->showFornecedores();
        $this->formasPagamento = $formaPagamentoService->show();
    }

    public function rules(){
        return [
            'entidade_id' => 'required|exists:entidade,id',
            'conta_id' => 'nullable|exists:conta,id',
            'categoria_financeira_id' => 'nullable|exists:categoria_financeira,id',
            'centro_custo_id' => 'nullable|exists:centro_custo,id',
            'numero_nf' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|min:2|max:5000',
            'descricao' => 'required|string|min:2|max:255',
            'valor_total' => 'required|numeric',
            'data_emissao' => 'nullable|date',
            'data_vencimento' => 'required|date',
            'quantidade_parcelas' => 'required|integer|min:1|max:24',
        ];
    }

    public function messages(){
        return [
            'entidade_id.required' => 'A entidade é obrigatória.',
            'entidade_id.exists' => 'A entidade informada é inválida.',

            'conta_id.exists' => 'A conta informada é inválida.',

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

            'data_emissao.date' => 'A data de emissão deve ser uma data válida.',

            'data_vencimento.required' => 'A data de vencimento é obrigatória.',
            'data_vencimento.date' => 'A data de vencimento deve ser uma data válida.',
            'data_vencimento.date_format' => 'A data de vencimento deve estar no formato YYYY-MM-DD.',

            'quantidade_parcelas.required' => 'A quantidade de parcelas é obrigatória.',
            'quantidade_parcelas.integer' => 'A quantidade de parcelas deve ser um número inteiro.',
            'quantidade_parcelas.min' => 'A quantidade de parcelas deve ser no mínimo :min.',
            'quantidade_parcelas.max' => 'A quantidade de parcelas não pode ser maior que :max.',
        ];
    }

    public function gerarParcelas(ParcelaService $service){
        try{
            if(!$this->valor_total || !$this->quantidade_parcelas || !$this->data_vencimento){
                $this->dispatch('toast-error', 'Verifique se os campos Valor Total, Parcelas e 1º Vencimento estão preenchidos.');
            }else{
                if(!empty($this->parcelas)){
                    $this->parcelas = [];
                }
                $this->parcelas = $service->gerarParcelas($this->valor_total, $this->quantidade_parcelas, $this->data_vencimento);
            }
            $this->dispatch('toast-message', 'Projeção de parcelas geradas com successo!');
        }catch(\Exception $e){
            $this->dispatch('toast-error', 'Erro ao gerar parcelas.');
            \Log::error("Erro ao gerar parcelas: ", ['erro' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.titulo.contas-pagar.create-titulo');
    }
}
