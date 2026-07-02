<?php

namespace App\Livewire\Relatorio\Modais;

use Carbon\Carbon;
use Livewire\Component;

class DespesasModal extends Component
{
    public string $dataInicio = '';

    public string $dataFim = '';

    public bool $compararPeriodoAnterior = true;

    public string $base = 'pago';

    public string $agrupamento = 'mensal';

    public string $detalhe = 'consolidado';

    public bool $incluirCanceladas = false;

    public string $formatoSaida = 'pdf';

    public function mount(): void
    {
        $this->dataInicio = now()->startOfMonth()->format('Y-m-d');
        $this->dataFim = now()->endOfMonth()->format('Y-m-d');
    }

    public function fechar(): void
    {
        $this->dispatch('fechar-modal-despesas');
    }

    public function gerar(): void
    {
        $this->resetErrorBag();

        try {
            $inicio = Carbon::parse($this->dataInicio)->startOfDay();
            $fim = Carbon::parse($this->dataFim)->endOfDay();
        } catch (\Throwable) {
            $this->addError('dataInicio', 'Informe datas válidas.');

            return;
        }

        if ($inicio->gt($fim)) {
            $this->addError('dataFim', 'A data final deve ser igual ou posterior à inicial.');

            return;
        }

        // Reservado: montar query / job de geração usando período, flags e formato.
    }

    public function render()
    {
        return view('livewire.relatorio.modais.despesas-modal');
    }
}
