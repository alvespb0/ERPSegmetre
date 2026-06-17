<?php

class SicoobService
{
    public function resolver(string $operacao, Integracao $integracao) {
        return match ($operacao) {
            'cobranca'  => app(SicoobCobrancaService::class, ['integracao' => $integracao]),
            'pagamento' => app(SicoobPagamentoService::class, ['integracao' => $integracao]),
            'saldo'     => app(SicoobSaldoService::class, ['integracao' => $integracao]),
            'dda'       => app(SicoobDDAService::class, ['integracao' => $integracao]),
            default     => throw new Exception('Operação inválida'),
        };
    }
}

?>