<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;

use App\Models\Parcela;

use App\Services\ParcelaService;
use App\Services\TituloFinanceiroService;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;

class EditarParcela extends Component
{
    public $parcela;
    public $editDataVencimento;
    public $editDescricao;
    public $editDataEmissao;
    public $editNumeroNf;
    public $editCategoriaId;
    public $editCentroCustoId;
    public $editObservacoes;
    public $categorias;
    public $centrosCusto;

    public function mount($parcelaId, CategoriaFinanceiraService $categoriaFinanceiraService, CentroCustoService $centroCustoService){
        $this->categorias = $categoriaFinanceiraService->showReceitas();
        $this->centrosCusto = $centroCustoService->show();

        $this->parcela = Parcela::with('titulo.entidade', 'movimentacoes')->findOrFail($parcelaId);
        
        $titulo = $this->parcela->titulo;

        $this->editDataVencimento = $this->parcela->data_vencimento;
        $this->editDescricao = $titulo->descricao;
        $this->editDataEmissao = $titulo->data_emissao;
        $this->editNumeroNf = $titulo->numero_nf;
        $this->editCategoriaId = $titulo->categoria_financeira_id;
        $this->editCentroCustoId = $titulo->centro_custo_id;
        $this->editObservacoes = $titulo->observacoes;
    }

    public function salvarEdicao(ParcelaService $parcelaService, TituloFinanceiroService $tituloService){
        $this->validate([
            'editDataVencimento' => 'required|date',
            'editDescricao' => 'required|string|max:255',
            'editDataEmissao' => 'required|date',
            'editNumeroNf' => 'nullable|string|max:50',
            'editCategoriaId' => 'nullable|exists:categoria_financeira,id',
            'editCentroCustoId' => 'nullable|exists:centro_custo,id',
            'editObservacoes' => 'nullable|string',
        ], [
            'editDataVencimento.required' => 'A data de vencimento é obrigatória.',
            'editDataVencimento.date' => 'Informe uma data de vencimento válida.',

            'editDescricao.required' => 'A descrição é obrigatória.',
            'editDescricao.string' => 'A descrição deve ser um texto válido.',
            'editDescricao.max' => 'A descrição pode ter no máximo 255 caracteres.',

            'editDataEmissao.required' => 'A data de emissão é obrigatória.',
            'editDataEmissao.date' => 'Informe uma data de emissão válida.',

            'editNumeroNf.string' => 'O número da nota fiscal deve ser um texto válido.',
            'editNumeroNf.max' => 'O número da nota fiscal pode ter no máximo 50 caracteres.',

            'editCategoriaId.exists' => 'A categoria selecionada é inválida.',

            'editCentroCustoId.exists' => 'O centro de custo selecionado é inválido.',

            'editObservacoes.string' => 'As observações devem ser um texto válido.',
        ]);
        
        $parcelaService->update([
            'data_vencimento' => $this->editDataVencimento
        ], $this->parcela->id);

        $tituloService->update([
            'descricao' => $this->editDescricao,
            'data_emissao' => $this->editDataEmissao,
            'numero_nf' => $this->editNumeroNf,
            'categoria_financeira_id' => $this->editCategoriaId ?? null,
            'centro_custo_id' => $this->editCentroCustoId ?? null,
            'observacoes' => $this->editObservacoes,
        ], $this->parcela->titulo->id);

        
        $this->reset([
            'editDataVencimento', 'editDescricao', 'editDataEmissao', 
            'editNumeroNf', 'editCategoriaId', 'editCentroCustoId', 'editObservacoes'
        ]);
        
        $this->dispatch('fechar-modal-edicao');

        $this->dispatch('toast-message', 'Título/Parcela atualizada com sucesso!');
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.editar-parcela');
    }
}
