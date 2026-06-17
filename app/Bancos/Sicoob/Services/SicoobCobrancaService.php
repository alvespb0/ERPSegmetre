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

    public function gerarBoletoSandbox(BoletoCobranca $boleto){
        /* BLOCO AUTH, EM SANDBOX NAO PRECISA DE LOGICA DE OAUTH, SERÁ FEITO DEPOIS, POR ISSO FUNÇÃO COM SANDBOX NO NOME */
        $client_id = $this->integracao->credenciais->client_id; # em SANDBOX isso só vai ser passado no header client_id => $client_id
        $access_token = $this->integracao->credenciais->access_token; # em SANDBOX atua como um bearer

        /* BLOCO DE INSTANCIA DE RELAÇÕES */
        $configuracaoCobranca = $boleto->configuracaoCobranca;
        $conta = $configuracaoCobranca->conta;
        $parcela = $boleto->parcela;
        $pagador = $boleto->parcela->titulo->entidade;
        $enderecoPagador = $pagador->enderecos()->first();

        /* BLOCO DE PARAMETROS DA QUERY */
        $contaCorrente = $this->separarDv($conta->conta);

        $codigoCliente = $configuracaoCobranca->codigo_cedente;
        $codigoModalidade = $boleto->modalidade; # modalidade 1 simples, 2 vinculada etc
        $numeroContaCorrente = $contaCorrente['num']; # por regra de negocio SEMPRE é salvo com digito verificador - 
        $codigoEspecie = $boleto->especie_documento; # especie do documento , DM, CH, etc.
        $dataEmissao = $boleto->data_registro ?? Carbon::today()->toDateString(); # data de emissão do boleto
        $seuNumero = $boleto->numero_documento; # Número identificador do boleto no sistema do beneficiário
        $emissaoBoleto = 1; # Quem emite o boleto, vou deixar hardcodado 1 só para sandbox
        $distribuicaoBoleto = 2; # Quem distribui o boleto, esse é de fato o usuario do sistema, então 2 hardcodado mesmo.
        $valorBoleto = $parcela->valor; # Valor nominal do boleto
        $dataVencimento = $parcela->data_vencimento; # Data de vencimento da duplicata
        $tipoDesconto = 0; # hardcodado 0 mesmo, sem desconto, está na analise se vou deixar a reverie do cliente mais tarde
        $tipoMulta = $boleto->codigo_multa; # 0 isento, 1 valor fixo, 2 percentual, é enum no bd
        
        if($tipoMulta != '0'){
            $valorMulta = $boleto->valor_multa; # valor da multa
            $dataMulta = $boleto->data_multa; # data da multa
        }

        $tipoJuros = $this->mapearTipoJuros($boleto->codigo_juros); # 1 valor por dia, 2 taxa mensal, 3 isento

        if($tipoJuros != '3'){
            $dataJuro = Carbon::parse($dataVencimento->data_vencimento)->addDay(); # inicio de cobrança de juro a partir de 1 dia após o vencimento
            $valorJuro = $boleto->valor_juros; # valor dos juros
        }
        
        $numParcela = $parcela->numero_parcela;

        $pagador = [
            'numeroCpfCnpj' => preg_replace('/[.\/-]/', '', $this->pagador->cpf_cnpj),
            'nome' => $pagador->razao_social,
            'endereco' => $enderecoPagador->rua . ', ' . ($enderecoPagador->numero ?: 'S/N'),
            'bairro' => $enderecoPagador->bairro,
            'cidade' => $enderecoPagador->cidade,
            'cep' => $enderecoPagador->cep,
            'uf' => $enderecoPagador->uf
        ];

        /* BLOCO QUERY */
        $response = Http::withToken($access_token)
            ->withHeaders([
                'client_id' => $client_id
            ])
            ->post($this->integracao->endPoint, [
                'numeroCliente' => $codigoCliente,
                'codigoModalidade' => $codigoModalidade,
                'numeroContaCorrente' => $numeroContaCorrente,
                'codigoEspecieDocumento' => $codigoEspecie,
                'dataEmissao' => $dataEmissao,
                'seuNumero' => $seuNumero,
                'identificacaoEmissaoBoleto' => $emissaoBoleto,
                'identificacaoDistribuicaoBoleto' => $distribuicaoBoleto,
                'valor' => $valorBoleto,
                'dataVencimento' => $dataVencimento,

            ]);
        
    }

    private function separarDv($valor): array{
        list($num, $dv) = explode('-', $valor);

        return [
            'num' => $num,
            'dv' => $dv
        ];
    }
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