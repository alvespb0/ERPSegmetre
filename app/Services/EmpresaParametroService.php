<?php

namespace App\Services;

use App\Models\EmpresaParametro;

class EmpresaParametroService
{
    public function store(array $dados, ?array $certificadoDados = null, $certificadoArquivo = null): EmpresaParametro
    {
        $empresa = EmpresaParametro::create($dados);

        if ($certificadoArquivo || ! empty($certificadoDados['nome_certificado'])) {
            (new CertificadoDigitalService())->storeOrUpdate(
                $empresa->id,
                $certificadoDados ?? [],
                $certificadoArquivo
            );
        }

        return $empresa;
    }

    public function update(array $dados, $id, ?array $certificadoDados = null, $certificadoArquivo = null): bool
    {
        $empresa = EmpresaParametro::findOrFail($id);

        $atualizado = $empresa->update([
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

        if ($certificadoArquivo || ! empty($certificadoDados)) {
            (new CertificadoDigitalService())->storeOrUpdate(
                $empresa->id,
                $certificadoDados ?? [],
                $certificadoArquivo
            );
        }

        return $atualizado;
    }

    public function show(): ?EmpresaParametro
    {
        return EmpresaParametro::with('certificadoDigital')->first();
    }
}
