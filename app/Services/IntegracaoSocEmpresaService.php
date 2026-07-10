<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\IntegracaoSocEmpresa;

class IntegracaoSocEmpresaService
{
    public function store(array $dados){
        return IntegracaoSocEmpresa::create([
            'entidade_id' => $dados['entidade_id'],
            'codigo_empresa' => $dados['codigo_empresa'],
            'codigo_unidade' => $dados['codigo_unidade'] ?? null,
            'nome_unidade' => $dados['nome_unidade'] ?? null,
        ]);
    }

    
}

?>