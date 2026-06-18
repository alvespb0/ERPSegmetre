<?php

namespace App\Bancos\Sicoob\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

use App\Models\BoletoCobranca;
use App\Models\Integracao;

class SicoobCobrancaService
{
    protected $integracao;

    public function __construct(Integracao $integracao){
        $this->integracao = $integracao;
    }

    /**
     * Registra um boleto na API de cobrança do Sicoob Sandbox.
     *
     * Atualmente utiliza autenticação simplificada do ambiente
     * Sandbox, enviando client_id no header e access_token
     * diretamente como Bearer Token.
     *
     * O payload é montado a partir dos dados do boleto,
     * parcela, conta bancária, configuração de cobrança
     * e pagador.
     *
     * @param BoletoCobranca $boleto Boleto a ser registrado.
     *
     * @return array Resposta retornada pela API do Sicoob.
     */
    public function gerarBoletoSandbox(BoletoCobranca $boleto){
        /* BLOCO AUTH, EM SANDBOX NAO PRECISA DE LOGICA DE OAUTH, SERÁ FEITO DEPOIS, POR ISSO FUNÇÃO COM SANDBOX NO NOME */
        $client_id = $this->integracao->credenciais->client_id; # em SANDBOX isso só vai ser passado no header client_id => $client_id
        $access_token = $this->integracao->credenciais->access_token; # em SANDBOX atua como um bearer

        $payload = $this->payloadMount($boleto);

        \Log::debug(['Payload de geração de boleto sandbox' => $payload]);
        
        /* BLOCO QUERY */
        $response = Http::withToken($access_token)
            ->withHeaders([
                'client_id' => $client_id,
            ])
            ->post(
                $this->integracao->endpoint . 'cobranca-bancaria/v3/boletos',
                $payload
            );
        
        return $response->json();
    }

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
    private function payloadMount(BoletoCobranca $boleto): array{
        return array_merge(
            $this->mountDadosTitulo($boleto),
            $this->mountMulta($boleto),
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
    private function mountDadosTitulo(BoletoCobranca $boleto): array{
        $configuracao = $boleto->configuracaoCobranca;
        $conta = $configuracao->conta;
        $parcela = $boleto->parcela;

        $contaCorrente = $this->separarDv(
            $conta->conta
        );

        return [
            'numeroCliente' => (int) $configuracao->codigo_cedente,
            'codigoModalidade' => (int) $boleto->modalidade,
            'numeroContaCorrente' => (int) $contaCorrente['num'],
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
    private function mountMulta(BoletoCobranca $boleto): array{
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
    private function mountJuros(BoletoCobranca $boleto): array{
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
    private function mountPagador(BoletoCobranca $boleto): array{
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
                'cep' => $endereco->cep,
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