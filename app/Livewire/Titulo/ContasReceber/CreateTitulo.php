<?php

namespace App\Livewire\Titulo\ContasReceber;

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

use Illuminate\Support\Facades\DB;

class CreateTitulo extends Component
{
    public $entidade_id, $descricao, $status, $valor_total, $data_emissao, $data_vencimento, $quantidade_parcelas;
    public $conta_id, $categoria_financeira_id, $centro_custo_id, $numero_nf, $observacoes;
    public $parcelas = [];

    public function rules(){
        return [
            'entidade_id' => 'required|exists:entidade,id',
            'conta_id' => 'nullable|exists:conta,id',
            'categoria_financeira_id' => 'nullable|exists:categoria_financeira,id',
            'centro_custo_id' => 'nullable|exists:centro_custo,id',
            'numero_nf' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|min:2|max:5000',
            'descricao' => 'required|string|min:2|max:255',
            'status' => 'required|in:aberto,parcial,pago,cancelado',
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

            'status' => 'O campo de status é obrigatório',
            'status.in' => 'O status deve ser: aberto, parcial, pago ou cancelado.',

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

    public function submit(TituloFinanceiroService $tituloService, ParcelaService $parcelaService){
        try{
            $data = $this->validate();

            $tituloData = [
                'centro_custo_id' => $data['centro_custo_id'] ?? null,
                'categoria_financeira_id' => $data['categoria_financeira_id'] ?? null,
                'conta_id' => $data['conta_id'] ?? null,
                'entidade_id' => $data['entidade_id'],
                'descricao' => $data['descricao'],
                'observacoes' => $data['observacoes'] ?? null,
                'numero_nf' => $data['numero_nf'] ?? null,
                'valor_total' => $data['valor_total'],
                'data_emissao' => $data['data_emissao'] ?? Carbon::today(),
                'tipo' => 'receber',
                'status' => $data['status'],
            ];

            if(!empty($this->parcelas)){
                $this->parcelas = [];
            }

            $this->parcelas = $parcelaService->gerarParcelas($data['valor_total'], $data['quantidade_parcelas'], $data['data_vencimento']); #executado novamente para caso o usuário tenha trocado o valor_total e não tenha clicado em gerar parcelas.

            DB::transaction(function () use ($tituloData, $parcelaService, $tituloService) {
                $titulo = $tituloService->store($tituloData); 
                
                foreach($this->parcelas as $index => $parcela){
                    $parcelaService->store([
                        'titulo_financeiro_id' => $titulo->id,
                        'numero_parcela' => $parcela['parcela_numero'],
                        'valor' => $parcela['valor_parcela'],
                        'data_vencimento' => $parcela['data_vencimento_parcela'],
                        'status' => $this->status ? $this->status : 'aberto'
                    ]);
                }
            });

            $this->dispatch('toast-message', 'Título e parcelas criados com sucesso!');
        }catch (ValidationException $e) {
            throw $e;
        }catch(\Exception $e){
            $this->dispatch('toast-error', 'Erro ao salvar título financeiro.');
            \Log::error("Erro ao salvar título: ", ['erro' => $e->getMessage()]);
        }
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

    public function render(
        ContaService $contaService,
        CategoriaFinanceiraService $categoriaFinanceiraService,
        CentroCustoService $centroCustoService,
        EntidadeService $entidadeService,
        FormaPagamentoService $formaPagamentoService
    )
    {
        $contas = $contaService->show();
        $categorias = $categoriaFinanceiraService->showReceitas();
        $centrosCusto = $centroCustoService->show();
        $entidades = $entidadeService->showClientes();
        $formasPagamento = $formaPagamentoService->show();

        return view('livewire.titulo.contas-receber.create-titulo', [
            'contas' => $contas,
            'categoriasFinanceira' => $categorias,
            'centrosCusto' => $centrosCusto,
            'entidades' => $entidades,
            'formasPagamento' => $formasPagamento,
        ]);

    }
}
