<?php
namespace App\Services\Integracoes\SOC;

use App\Models\Integracao;

class SOCService
{
    public function resolver(string $operacao, Integracao $integracao) {
        return match ($operacao) {
            'exames'  => app(SOCExamesService::class, ['integracao' => $integracao]),
            'laudos' => app(SOCExamesService::class, ['integracao' => $integracao]),
            default     => throw new Exception('Operação inválida'),
        };
    }
}

?>