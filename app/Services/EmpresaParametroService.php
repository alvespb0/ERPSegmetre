<?php

namespace App\Services;

use App\Models\EmpresaParametro;

class EmpresaParametroService
{
    public function store(array $dados): EmpresaParametro
    {
        return EmpresaParametro::create($dados);
    }

    public function update(array $dados, $id): bool
    {
        $empresa = EmpresaParametro::findOrFail($id);

        return $empresa->update([
            'razao_social' => $dados['razao_social'],
            'nome_fantasia' => $dados['nome_fantasia'],
            'cnpj' => $dados['cnpj'],
            'inscricao_estadual' => $dados['inscricao_estadual'],
            'inscricao_municipal' => $dados['inscricao_municipal'],
            'cnae_principal' => $dados['cnae_principal'],
            'cep' => $dados['cep'],
            'logradouro' => $dados['logradouro'],
            'bairro' => $dados['bairro'],
            'numero' => $dados['numero'],
            'complemento' => $dados['complemento'],
            'cidade' => $dados['cidade'],
            'uf' => $dados['uf'],
            'telefone' => $dados['telefone'],
            'email_financeiro' => $dados['email_financeiro'],
            'logo_path' => $dados['logo_path'] ?? $empresa->logo_path,
        ]);
    }

    public function show(): ?EmpresaParametro
    {
        return EmpresaParametro::first();
    }
}
