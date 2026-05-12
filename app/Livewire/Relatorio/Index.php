<?php

namespace App\Livewire\Relatorio;

use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public ?string $painelAberto = null;

    public bool $openModalDre = false;

    /** @var array<int, array<string, string>> */
    public array $relatorios = [];

    public function mount(): void
    {
        $this->relatorios = [
            [
                'id' => 'dre',
                'titulo' => 'DRE',
                'descricao' => 'Demonstração do Resultado do Exercício, apresentando receitas, custos, despesas e o resultado líquido do período selecionado.',
            ],
            [
                'id' => 'fluxo-caixa',
                'titulo' => 'Fluxo de caixa',
                'descricao' => 'Controle das entradas e saídas financeiras, permitindo acompanhar saldo, liquidez e projeções por período.',
            ],
            [
                'id' => 'analise-financeira',
                'titulo' => 'Análise financeira',
                'descricao' => 'Indicadores e comparativos financeiros, incluindo margens, evolução de resultados e composição de receitas e despesas.',
            ],
            [
                'id' => 'vendas',
                'titulo' => 'Vendas',
                'descricao' => 'Consolidação do faturamento e das receitas obtidas, com acompanhamento da performance comercial ao longo do tempo.',
            ],
            [
                'id' => 'despesas',
                'titulo' => 'Despesas',
                'descricao' => 'Detalhamento das despesas e pagamentos realizados, com filtros por categoria, centro de custo ou fornecedor.',
            ],
        ];
    }

    public function togglePainel(string $relatorioId): void
    {
        if (! $this->relatorioIdValido($relatorioId)) {
            return;
        }

        $this->painelAberto = $this->painelAberto === $relatorioId ? null : $relatorioId;
    }

    public function irParaGeracao(string $relatorioId): void
    {
        if (! $this->relatorioIdValido($relatorioId)) {
            return;
        }

        if ($relatorioId === 'dre') {
            $this->openModalDre = true;

            return;
        }

        // Reservado: modais ou rotas dos demais relatórios (fluxo de caixa, etc.).
    }

    #[On('fechar-modal-dre')]
    public function fecharModalDre(): void
    {
        $this->openModalDre = false;
    }

    protected function relatorioIdValido(string $relatorioId): bool
    {
        return in_array($relatorioId, array_column($this->relatorios, 'id'), true);
    }

    public function render()
    {
        return view('livewire.relatorio.index');
    }
}
