<?php
namespace App\Bancos\Sicoob\Cnab240;

use App\Models\Conta;
use App\Models\BoletoCobranca;
use App\Models\Entidade;
use Carbon\Carbon;

class SegmentoQ
{
    public Conta $conta;
    public BoletoCobranca $boleto;
    public Entidade $pagador;
    public $numeroRegistroQ;
    public $numeroLote;
    public $codigoMovimentacao;
    public $enderecoPagador;

    public function __construct(Conta $conta, BoletoCobranca $boleto, $numeroLote, $numeroRegistroQ, $codigoMovimentacao){
        $this->conta = $conta;
        $this->boleto = $boleto;
        $this->numeroLote = $numeroLote;
        $this->numeroRegistroQ = $numeroRegistroQ;
        $this->codigoMovimentacao = $codigoMovimentacao;
        $this->pagador = $this->boleto->parcela->titulo->entidade;
        $this->enderecoPagador = $this->pagador->enderecos()->first();
    }

    public function montar(): string {
        $cep = $this->separarCep($this->enderecoPagador->cep);
        $logradouro = $this->enderecoPagador->rua . ', ' .
            ($this->enderecoPagador->numero ?: 'S/N');

        $pos01_03 = Cnab240Formatter::numerico($this->conta->banco->numero_banco, 3); # Codigo do banco; tipo numerico;
        $pos04_07 = Cnab240Formatter::numerico($this->numeroLote, 4); # Lote do serviço, o mesmo só é sequencial dentro do mesmo arquivo (tem que ser o mesmo do headerLote); tipo numerico
        $pos08_08 = Cnab240Formatter::numerico("3", 1); # Tipo de registro, hardcodado, sempre será 3; tipo numerico
        $pos09_13 = Cnab240Formatter::numerico($this->numeroRegistroQ, 5); # Numero sequencial do registro P do lote, sequencial dentro do mesmo arquivo (ou seja, 1 remessa com 3 movimentações de segmento Q 1, 2, 3); tipo numerico
        $pos14_14 = Cnab240Formatter::alfa("Q", 1); # Cod segmento do registro detalhe: P; tipo alfa
        $pos15_15 = Cnab240Formatter::alfa("", 1); # Uso exclusivo FEBRABAN; tipo alfa
        $pos16_17 = Cnab240Formatter::numerico($this->codigoMovimentacao, 2); # Codigo de movimentação da remessa, é tratado (se realmente existe, etc) em uma camada acima; tipo numerico
        $pos18_18 = Cnab240Formatter::numerico(strlen(preg_replace('/[.\/-]/', '', $this->pagador->cpf_cnpj)) > 11 ? "2" : "1", 1); # Tipo de inscrição do pagador; tipo numerico
        $pos19_33 = Cnab240Formatter::numerico(preg_replace('/[.\/-]/', '', $this->pagador->cpf_cnpj), 15); # Inscrição do pagador; tipo numerico
        $pos34_73 = Cnab240Formatter::alfa($this->pagador->razao_social, 40); # Nome do pagador; tipo alfa
        $pos74_113 = Cnab240Formatter::alfa($logradouro, 40); # Endereço do Pagador; tipo alfa
        $pos114_128 = Cnab240Formatter::alfa($this->enderecoPagador->bairro, 15); # Bairro do pagador; tipo alfa
        $pos129_133 = Cnab240Formatter::numerico($cep['cep'], 5); # CEP pagador; tipo numerico
        $pos134_136 = Cnab240Formatter::numerico($cep['sufixo'], 3); # sufixo CEP pagador; tipo numerico
        $pos137_151 = Cnab240Formatter::alfa($this->enderecoPagador->cidade, 15); # Cidade do pagador; tipo alfa
        $pos152_153 = Cnab240Formatter::alfa($this->enderecoPagador->uf, 2); # UF da cidade do pagador; tipo alfa
        $pos154_154 = Cnab240Formatter::numerico("0", 1); # Tipo de inscrição do avalista, inicialmente vou deixar sem essa opção para o usuário, mas já coloquei no planejamento futuro 0 = isento; tipo numerico
        $pos155_169 = Cnab240Formatter::numerico("0", 15); # Inscrição do avalista; tipo numerico
        $pos170_209 = Cnab240Formatter::alfa("", 40); # Nome do avalista; tipo alfa
        $pos210_212 = Cnab240Formatter::numerico("000", 3); # Caso seja optante por banco correspondente, não é o caso, vai ser hardcodado 000 e mandado para planejamentos; tipo numerico
        $pos213_232 = Cnab240Formatter::alfa("", 20); # Nome do banco correspondente; tipo alfa
        $pos233_240 = Cnab240Formatter::alfa("", 8); # Uso exclusivo FEBRABAN; tipo alfa

        $linha =
            $pos01_03 .
            $pos04_07 .
            $pos08_08 .
            $pos09_13 .
            $pos14_14 .
            $pos15_15 .
            $pos16_17 .
            $pos18_18 .
            $pos19_33 .
            $pos34_73 .
            $pos74_113 .
            $pos114_128 .
            $pos129_133 .
            $pos134_136 .
            $pos137_151 .
            $pos152_153 .
            $pos154_154 .
            $pos155_169 .
            $pos170_209 .
            $pos210_212 .
            $pos213_232 .
            $pos233_240;

        if (strlen($linha) !== 240) {
            throw new \Exception(
                'Segmento Q inválido. Tamanho: ' . strlen($linha)
            );
        }

        \Log::debug('Debug de segmento Q cnab', ['length' => strlen($linha), 'linha' => $linha]);

        return $linha;
    }

    private function separarCep(string $cep): array{
        $cep = preg_replace('/\D/', '', $cep);

        return [
            'cep' => substr($cep, 0, 5),
            'sufixo' => substr($cep, 5, 3),
        ];
    }
}