<?php

namespace App\Livewire\Relatorio;

use Livewire\Component;

class Index extends Component
{
    /** `id` do relatório com painel expandido, ou null se todos fechados. */
    public ?string $painelAberto = null;

    /** @var array<int, array<string, string>> */
    public array $relatorios = [
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

        // Reservado: redirecionar para filtros / geração usando $relatorioId.
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
