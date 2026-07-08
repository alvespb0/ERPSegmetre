<?php

namespace App\Livewire\Titulo\ContasReceber;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\EntidadeService;
use App\Services\FormaPagamentoService;
use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;

use Illuminate\Support\Facades\DB;

class CreateTitulo extends Component
{
    public $entidade_id, $descricao, $valor_total, $data_emissao, $data_vencimento, $quantidade_parcelas;
    public $categoria_financeira_id, $centro_custo_id, $numero_nf, $observacoes;
    public $categoriasFinanceira, $centrosCusto, $entidades, $formasPagamento;
    public $parcelas = [];
    public $venda_recorrente = false;
    public $quantidade_recorrencias;
    public $intervalo_recorrencia = '1m';

    public function mount(
        CategoriaFinanceiraService $categoriaFinanceiraService,
        CentroCustoService $centroCustoService,
        EntidadeService $entidadeService,
        FormaPagamentoService $formaPagamentoService
    ){
        $this->categoriasFinanceira = $categoriaFinanceiraService->showReceitas();
        $this->centrosCusto = $centroCustoService->show();
        $this->entidades = $entidadeService->showClientes();
        $this->formasPagamento = $formaPagamentoService->show();
    }

    public function rules(){
        $rules = [
            'entidade_id' => 'required|exists:entidade,id',
            'categoria_financeira_id' => 'nullable|exists:categoria_financeira,id',
            'centro_custo_id' => 'nullable|exists:centro_custo,id',
            'numero_nf' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|min:2|max:5000',
            'descricao' => 'required|string|min:2|max:255',
            'valor_total' => 'required|numeric',
            'data_emissao' => 'nullable|date',
            'data_vencimento' => 'required|date',
            'quantidade_parcelas' => 'required|integer|min:1|max:24',
            'venda_recorrente' => 'boolean',
            'quantidade_recorrencias' => 'nullable|required_if:venda_recorrente,true|integer|min:2|max:60',
            'intervalo_recorrencia' => 'nullable|required_if:venda_recorrente,true|in:7d,14d,21d,30d,45d,60d,90d,1m,2m,3m,6m,12m',
        ];

        return $rules;
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

            'data_emissao.date' => 'A data de emissão deve ser uma data válida.',

            'data_vencimento.required' => 'A data de vencimento é obrigatória.',
            'data_vencimento.date' => 'A data de vencimento deve ser uma data válida.',
            'data_vencimento.date_format' => 'A data de vencimento deve estar no formato YYYY-MM-DD.',

            'quantidade_parcelas.required' => 'A quantidade de parcelas é obrigatória.',
            'quantidade_parcelas.integer' => 'A quantidade de parcelas deve ser um número inteiro.',
            'quantidade_parcelas.min' => 'A quantidade de parcelas deve ser no mínimo :min.',
            'quantidade_parcelas.max' => 'A quantidade de parcelas não pode ser maior que :max.',

            'quantidade_recorrencias.required' => 'Informe a quantidade de recorrências.',
            'quantidade_recorrencias.integer' => 'A quantidade de recorrências deve ser um número inteiro.',
            'quantidade_recorrencias.min' => 'A venda recorrente deve ter no mínimo :min ocorrências.',
            'quantidade_recorrencias.max' => 'A quantidade de recorrências não pode ser maior que :max.',

            'intervalo_recorrencia.required' => 'Selecione o intervalo entre as vendas.',
            'intervalo_recorrencia.in' => 'O intervalo selecionado é inválido.',
        ];
    }

    public function updatedVendaRecorrente($value): void
    {
        if (! $value) {
            $this->quantidade_recorrencias = null;
            $this->intervalo_recorrencia = '1m';
        }
    }

    /**
     * Salva o título financeiro e gera automaticamente suas parcelas.
     *
     * Fluxo:
     * - Valida os dados informados no formulário.
     * - Inicia uma transação para garantir a integridade dos dados.
     * - Caso seja uma venda recorrente, cria um título para cada recorrência,
     *   ajustando a data de vencimento conforme o intervalo informado.
     * - Para cada título criado, gera e persiste suas respectivas parcelas.
     * - Em caso de sucesso, exibe uma mensagem de confirmação.
     * - Em caso de erro, realiza rollback da transação e registra o erro no log.
     *
     * @param TituloFinanceiroService $tituloService Serviço responsável pela criação do título financeiro.
     * @param ParcelaService $parcelaService Serviço responsável pela geração e persistência das parcelas.
     *
     * @throws \Illuminate\Validation\ValidationException Quando a validação do formulário falhar.
     *
     * @return void
     */
    public function submit(TituloFinanceiroService $tituloService, ParcelaService $parcelaService){
        try {
            $data = $this->validate();
            DB::transaction(function () use ($data, $tituloService, $parcelaService) {

                $quantidadeRecorrencias = !empty($data['venda_recorrente']) ? (int) $data['quantidade_recorrencias'] : 1; # se ambos campos null (caso por exemplo !venda_recorrente) entao 1

                for ($i = 0; $i < $quantidadeRecorrencias; $i++) {

                    $vencimentoAtual = $this->calcularProximaData(
                        $data['data_vencimento'], 
                        $data['intervalo_recorrencia'] ?? null, 
                        $i
                    );

                    # Adiciona um sufixo na descrição para identificar as recorrencas (Ex: Venda - 1/12)
                    $sufixoDescricao = $quantidadeRecorrencias > 1 ? " (Recorrência " . ($i + 1) . "/{$quantidadeRecorrencias})" : "";

                    $tituloData = [
                        'centro_custo_id' => $data['centro_custo_id'] ?? null,
                        'categoria_financeira_id' => $data['categoria_financeira_id'] ?? null,
                        'entidade_id' => $data['entidade_id'],
                        'descricao' => $data['descricao'] . $sufixoDescricao,
                        'observacoes' => $data['observacoes'] ?? null,
                        'numero_nf' => $data['numero_nf'] ?? null,
                        'valor_total' => $data['valor_total'],
                        'data_emissao' => $data['data_emissao'] ?? \Carbon\Carbon::today(),
                        'tipo' => 'receber',
                        'status' => 'ativo',
                    ];

                    $titulo = $tituloService->store($tituloData);

                    $parcelasGeradas = $parcelaService->gerarParcelas(
                        $data['valor_total'], 
                        $data['quantidade_parcelas'], 
                        $vencimentoAtual->format('Y-m-d')
                    );

                    foreach ($parcelasGeradas as $parcela) {
                        $parcelaService->store([
                            'titulo_financeiro_id' => $titulo->id,
                            'numero_parcela' => $parcela['parcela_numero'],
                            'valor' => $parcela['valor_parcela'],
                            'data_vencimento' => $parcela['data_vencimento_parcela'],
                            'status' => 'ativo'
                        ]);
                    }
                }
            });
            $this->dispatch('toast-message', 'Título e parcelas criados com sucesso!');
        }catch (ValidationException $e) {
            throw $e;
        }catch (\Exception $e) {
            $this->dispatch('toast-error', 'Erro ao salvar título financeiro.');
            \Log::error("Erro ao salvar título: ", ['erro' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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

    private function calcularProximaData($dataBase, $intervalo, $multiplicador){
        if (!$intervalo || $multiplicador === 0) {
            return Carbon::parse($dataBase);
        }

        $data = Carbon::parse($dataBase);

        switch ($intervalo) {
            case '7d': return $data->addDays(7 * $multiplicador);
            case '14d': return $data->addDays(14 * $multiplicador);
            case '21d': return $data->addDays(21 * $multiplicador);
            case '30d': return $data->addDays(30 * $multiplicador);
            case '45d': return $data->addDays(45 * $multiplicador);
            case '60d': return $data->addDays(60 * $multiplicador);
            case '90d': return $data->addDays(90 * $multiplicador);
            case '1m': return $data->addMonthsNoOverflow(1 * $multiplicador);
            case '2m': return $data->addMonthsNoOverflow(2 * $multiplicador);
            case '3m': return $data->addMonthsNoOverflow(3 * $multiplicador);
            case '6m': return $data->addMonthsNoOverflow(6 * $multiplicador);
            case '12m': return $data->addYearsNoOverflow(1 * $multiplicador);
            default: return $data;
        }
    } 

    public function render(){
        return view('livewire.titulo.contas-receber.create-titulo');
    }
}
