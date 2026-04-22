<?php

namespace App\Livewire\Modais\ContasReceber;

use Illuminate\Validation\ValidationException;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\Models\Parcela;

use App\Services\AnexoService;

class Anexos extends Component
{
    use WithFileUploads;

    public $parcela;
    public $titulo;
    public $anexosMovimentacoes;

    public $arquivo;
    public $tipoAnexo;
    public $descricaoAnexo;

    public $arquivoTitulo;
    public $tipoAnexoTitulo;
    public $descricaoAnexoTitulo;

    public function mount($parcelaId){
        $this->parcela = Parcela::with(['titulo' => function ($q) { $q->withCount('parcelas');}, 'movimentacoes.anexos'])
                        ->findOrFail($parcelaId);
        $this->titulo = $this->parcela->titulo;
        $this->anexosMovimentacoes = $this->parcela->movimentacoes->flatMap(function($movimentacao) {
            return $movimentacao->anexos->map(function($anexo) use ($movimentacao) {
                $anexo->movimentacao_id = $movimentacao->id;
                return $anexo;
            });
            
        });
    }

    public function salvarAnexoParcela(AnexoService $service){
        try {
            $this->validate([
                'arquivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'descricaoAnexo' => 'nullable|string|max:255',
                'tipoAnexo' => 'required|in:comprovante,pix,boleto,fatura,outros'
            ], [
                'arquivo.required' => 'O arquivo é obrigatório.',
                'arquivo.file' => 'O envio deve ser um arquivo válido.',
                'arquivo.mimes' => 'O arquivo deve estar no formato: PDF, JPG, JPEG ou PNG.',
                'arquivo.max' => 'O arquivo não pode ser maior que 5MB.',

                'descricaoAnexo.string' => 'A descrição deve ser um texto válido.',
                'descricaoAnexo.max' => 'A descrição não pode ter mais que 255 caracteres.',

                'tipoAnexo.required' => 'O tipo de anexo é obrigatório.',
                'tipoAnexo.in' => 'O tipo de anexo deve ser: comprovante, pix, boleto, fatura ou outros.',
            ]);

            $service->criarAnexoParcela($this->parcela, $this->arquivo, $this->tipoAnexo, $this->descricaoAnexo);

            $this->dispatch('toast-message', 'Anexo Incluído com sucesso.');

            $this->reset(['arquivo', 'descricaoAnexo', 'tipoAnexo']);
        } catch (ValidationException $e) {
                throw $e;
        } catch (\Exception $e) {
            \Log::error('Erro ao fazer o upload do anexo', [
                'exception' => $e->getMessage(),
            ]);
            $this->dispatch('toast-error', 'Erro ao anexar arquivo.');
        }
    }

    public function salvarAnexoTitulo(AnexoService $service){
        try {
            $this->validate([
                'arquivoTitulo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'descricaoAnexoTitulo' => 'nullable|string|max:255',
                'tipoAnexoTitulo' => 'required|in:comprovante,pix,boleto,fatura,outros'
            ], [
                'arquivoTitulo.required' => 'O arquivo é obrigatório.',
                'arquivoTitulo.file' => 'O envio deve ser um arquivo válido.',
                'arquivoTitulo.mimes' => 'O arquivo deve estar no formato: PDF, JPG, JPEG ou PNG.',
                'arquivoTitulo.max' => 'O arquivo não pode ser maior que 5MB.',

                'descricaoAnexoTitulo.string' => 'A descrição deve ser um texto válido.',
                'descricaoAnexoTitulo.max' => 'A descrição não pode ter mais que 255 caracteres.',

                'tipoAnexoTitulo.required' => 'O tipo de anexo é obrigatório.',
                'tipoAnexoTitulo.in' => 'O tipo de anexo deve ser: comprovante, pix, boleto, fatura ou outros.',
            ]);

            $service->criarAnexoTitulo($this->titulo, $this->arquivoTitulo, $this->tipoAnexoTitulo, $this->descricaoAnexoTitulo);

            $this->dispatch('toast-message', 'Anexo Incluído com sucesso.');

            $this->reset(['arquivoTitulo', 'descricaoAnexoTitulo', 'tipoAnexoTitulo']);
        } catch (ValidationException $e) {
                throw $e;
        } catch (\Exception $e) {
            \Log::error('Erro ao fazer o upload do anexo', [
                'exception' => $e->getMessage(),
            ]);
            $this->dispatch('toast-error', 'Erro ao anexar arquivo.');
        }
    }

    public function downloadAnexo($anexoId, AnexoService $service){
        return $service->download($anexoId);
    }

    public function excluirAnexo($anexoId, AnexoService $service){
        $service->destroy($anexoId);

        $this->dispatch('toast-message', 'Anexo excluído com sucesso.');
    
        $this->dispatch('fechar-modal-anexos');
    }

    public function fechar(){
        $this->dispatch('fechar-modal-anexos');
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.anexos');
    }
}
