<?php
namespace App\Factories;

use App\Models\Integracao;

class IntegracaoFactory
{
    public function make(Integracao $integracao, string $operacao){
        $provider = app($integracao->provider);

        return $provider->resolver($operacao, $integracao);
    }
}

?>