<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;

use App\Models\TituloFinanceiro;
use App\Models\Parcela;

use App\Services\BoletoCobrancaService;

class DetalhesTitulo extends Component
{
    public $titulo;

    public $parcelasSelecionadas = [];

    public function mount($tituloId){
        $this->titulo = TituloFinanceiro::with('parcelas')->findOrFail($tituloId);
    }

    public function gerarCobrancasLote(){
        $this->dispatch('abrir-modal-cobranca-lote', parcelas: $this->parcelasSelecionadas);
        $this->fechar();
    }

    public function podeGerarCobrancasLote(): bool{
        if (empty($this->parcelasSelecionadas)) {
            return false;
        }

        return $this->titulo->parcelas->whereIn('id', $this->parcelasSelecionadas)->contains(fn ($parcela) => !$parcela->possui_boleto_ativo);
    }

    public function podeBaixarCobrancasLote(): bool{
        if (empty($this->parcelasSelecionadas)) {
            return false;
        }

        return $this->titulo->parcelas->whereIn('id', $this->parcelasSelecionadas)->contains(fn ($parcela) => $parcela->possui_boleto_ativo);
    }
    
    public function baixarCobrancasLote(){
        try{
            $zip = new \ZipArchive(); # instancia um novo model de arquivo zip
            $zipName = storage_path('app/public/boletos.zip');
            $zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $parcelas = Parcela::whereIn('id', $this->parcelasSelecionadas)->get();

            foreach($parcelas as $parcela){
                $boleto = $parcela->boleto_ativo;

                if ($boleto && $boleto->pdf_path) {
                    $file = storage_path('app/public/' . $boleto->pdf_path);
                    
                    if (file_exists($file)) {
                        $zip->addFile($file, basename($file)); # da append nesse zip se o boleto existir de acordo com o path
                    }
                }
            }

            $zip->close();

            $this->dispatch('toast-message', 'Download de cobranças realizadas com sucesso');

            return response()->download($zipName)->deleteFileAfterSend();
        }catch (\Exception $e){
            \Log::error([
                'Erro ao fazer download de cobranca em lote' => $e->getMessage()
            ]);

            $this->dispatch('toast-error', 'Não foi possível fazer o download de todas as cobranças.');
        }
    }

    public function fechar(){
        $this->dispatch('fechar-modal-titulo');
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.detalhes-titulo');
    }
}
