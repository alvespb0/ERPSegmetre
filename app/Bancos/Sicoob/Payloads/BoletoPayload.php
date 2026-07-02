<?php
namespace App\Bancos\Sicoob\Payloads;

use App\Models\BoletoCobranca;
use Carbon\Carbon;

class BoletoPayload{
    /**
     * Monta o payload completo de registro do boleto.
     *
     * O payload é dividido em blocos menores para facilitar
     * manutenção, testes e futuras implementações de regras
     * específicas do banco.
     *
     * @param BoletoCobranca $boleto
     *
     * @return array Payload pronto para envio.
     */
    public function payloadMount(BoletoCobranca $boleto): array{
        return array_merge(
            $this->mountDadosTitulo($boleto),
            $this->mountMulta($boleto),
            $this->mountMensagensInstrucao($boleto),
            $this->mountJuros($boleto),
            $this->mountPagador($boleto)
        );
    }

    /**
     * Monta os dados principais do título de cobrança.
     *
     * Contém informações contratuais do beneficiário,
     * conta corrente de liquidação, valor, vencimento,
     * modalidade e demais dados obrigatórios para
     * registro do boleto.
     *
     * @param BoletoCobranca $boleto
     *
     * @return array
     */
    public function mountDadosTitulo(BoletoCobranca $boleto): array{
        $configuracao = $boleto->configuracaoCobranca;
        $conta = $configuracao->conta;
        $parcela = $boleto->parcela;
        
        $numeroConta = preg_replace('/-/', '', $conta->conta);

        return [
            'numeroCliente' => (int) $configuracao->codigo_cedente,
            'codigoModalidade' => (int) $boleto->modalidade,
            'numeroContaCorrente' => (int) $numeroConta,
            'codigoEspecieDocumento' => $boleto->especie_documento,
            'dataEmissao' => Carbon::parse(
                $boleto->data_registro
            )->toDateString(),
            'seuNumero' => $boleto->numero_documento,
            'identificacaoEmissaoBoleto' => 1,
            'identificacaoDistribuicaoBoleto' => 2,
            'valor' => (float) $parcela->valor,
            'dataVencimento' => Carbon::parse(
                $parcela->data_vencimento
            )->toDateString(),
            'tipoDesconto' => 0,
            'numeroParcela' => (int) $parcela->numero_parcela,
            'gerarPdf' => true
        ];
    }

    /**
     * Monta as informações de multa do boleto.
     *
     * Caso o boleto esteja configurado como isento,
     * os campos dependentes são enviados como null.
     *
     * @param BoletoCobranca $boleto
     *
     * @return array
     */
    public function mountMulta(BoletoCobranca $boleto): array{
        $tipoMulta = (int) $boleto->codigo_multa;

        return [
            'tipoMulta' => $tipoMulta,
            'dataMulta' => $tipoMulta
                ? Carbon::parse(
                    $boleto->data_multa
                )->toDateString()
                : null,
            'valorMulta' => $tipoMulta
                ? (float) $boleto->valor_multa
                : null,
        ];
    }

    /**
     * Monta as mensagens de instrução do boleto.
     *
     * O campo info_complementares é armazenado como texto
     * livre e convertido para o formato exigido pela API
     * do Sicoob, onde cada linha representa uma instrução.
     *
     * Exemplo:
     *
     * Pagável em qualquer banco
     * Não receber após vencimento
     * Cobrar multa conforme contrato
     *
     * Resultado:
     *
     * [
     *     'mensagensInstrucao' => [
     *         'Pagável em qualquer banco',
     *         'Não receber após vencimento',
     *         'Cobrar multa conforme contrato',
     *     ]
     * ]
     *
     * @param BoletoCobranca $boleto
     *
     * @return array
     */
    public function mountMensagensInstrucao(BoletoCobranca $boleto): array
    {
        if (blank($boleto->info_complementares)) {
            return [];
        }

        $mensagens = collect(
            preg_split('/\r\n|\r|\n/', $boleto->info_complementares)
        )
            ->map(fn ($linha) => trim($linha))
            ->filter()
            ->values()
            ->toArray();

        return [
            'mensagensInstrucao' => $mensagens
        ];
    }
    /**
     * Monta as informações de juros de mora.
     *
     * O início da cobrança é definido como um dia após
     * o vencimento do boleto, respeitando as regras
     * atualmente adotadas pelo sistema.
     *
     * Caso o título seja isento de juros, os campos
     * relacionados são enviados como null.
     *
     * @param BoletoCobranca $boleto
     *
     * @return array
     */
    public function mountJuros(BoletoCobranca $boleto): array{
        $tipoJuros = $this->mapearTipoJuros(
            (int) $boleto->codigo_juros
        );

        $dataVencimento = Carbon::parse(
            $boleto->parcela->data_vencimento
        );

        return [
            'tipoJurosMora' => $tipoJuros,

            'dataJurosMora' => $tipoJuros !== 3
                ? $dataVencimento
                    ->copy()
                    ->addDay()
                    ->toDateString()
                : null,

            'valorJurosMora' => $tipoJuros !== 3
                ? (float) $boleto->valor_juros
                : null,
        ];
    }

    /**
     * Monta os dados do pagador.
     *
     * Realiza a normalização do CPF/CNPJ removendo
     * caracteres especiais e monta a estrutura exigida
     * pela API do Sicoob.
     *
     * @param BoletoCobranca $boleto
     *
     * @return array
     */
    public function mountPagador(BoletoCobranca $boleto): array{
        $pagador = $boleto->parcela
            ->titulo
            ->entidade;
        $endereco = $pagador
            ->enderecos()
            ->first();
        return [
            'pagador' => [
                'numeroCpfCnpj' => preg_replace(
                    '/[.\/-]/',
                    '',
                    $pagador->cpf_cnpj
                ),
                'nome' => $pagador->razao_social,
                'endereco' => $endereco->rua . ', ' .
                    ($endereco->numero ?: 'S/N'),
                'bairro' => $endereco->bairro,
                'cidade' => $endereco->cidade,
                'cep' => preg_replace('/-/', '', $endereco->cep),
                'uf' => $endereco->uf,
            ]
        ];
    }

    /**
     * Separa número e dígito verificador de uma conta.
     *
     * Exemplo:
     * 32069-8
     *
     * Retorno:
     * [
     *   'num' => '32069',
     *   'dv' => '8'
     * ]
     *
     * @param string $valor
     *
     * @return array{num:string,dv:string}
     */
    private function separarDv($valor): array{
        list($num, $dv) = explode('-', $valor);

        return [
            'num' => $num,
            'dv' => $dv
        ];
    }

    /**
     * Converte o código interno de juros do sistema
     * para o padrão exigido pela API do Sicoob.
     *
     * Mapeamento:
     * 0 => 3 (Isento)
     * 1 => 1 (Valor por dia)
     * 2 => 2 (Taxa mensal)
     *
     * @param int $tipo
     *
     * @return int
     *
     * @throws \Exception Quando o tipo informado não existir.
     */
    private function mapearTipoJuros(int $tipo): int{
        return match ($tipo) {

            0 => 3, // Isento

            1 => 1, // Valor por dia

            2 => 2, // Taxa mensal

            default => throw new Exception(
                'Tipo de juros inválido.'
            )
        };
    }
}

?>