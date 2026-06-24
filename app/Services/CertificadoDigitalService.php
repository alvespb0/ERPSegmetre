<?php

namespace App\Services;

use App\Models\CertificadoDigital;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificadoDigitalService
{
    public function storeOrUpdate(int $empresaParametroId, array $dados, ?UploadedFile $arquivo = null): ?CertificadoDigital
    {
        if (! $arquivo && empty($dados['nome_certificado']) && empty($dados['senha'])) {
            return CertificadoDigital::where('empresa_parametro_id', $empresaParametroId)->first();
        }

        $certificado = CertificadoDigital::firstOrNew([
            'empresa_parametro_id' => $empresaParametroId,
        ]);

        if ($arquivo) {
            $senha = $dados['senha'] ?? null;

            if (! $senha) {
                throw new \InvalidArgumentException('A senha do certificado é obrigatória ao enviar um novo arquivo.');
            }

            $convertido = $this->converterPfxParaPem($arquivo->getRealPath(), $senha);

            if ($certificado->exists && $certificado->cert_path) {
                Storage::disk('local')->delete($certificado->cert_path);
            }

            $nomeArquivo = Str::uuid() . '.pem';
            $caminho = "certificados/{$empresaParametroId}/{$nomeArquivo}";
            Storage::disk('local')->put($caminho, $convertido['pem']);

            $certificado->cert_path = $caminho;
            $certificado->senha = Crypt::encryptString($senha);
            $certificado->titular = $convertido['titular'];
            $certificado->numero_serie = $convertido['numero_serie'];
            $certificado->cpf_cnpj = $convertido['cpf_cnpj'];
            $certificado->emitido_em = $convertido['emitido_em'];
            $certificado->vence_em = $convertido['vence_em'];
        } elseif (! empty($dados['senha'])) {
            if (! $certificado->exists || ! $certificado->cert_path) {
                throw new \InvalidArgumentException('Envie o arquivo do certificado para alterar a senha.');
            }

            $caminhoCompleto = Storage::disk('local')->path($certificado->cert_path);
            $metadados = $this->extrairMetadadosDoPem($caminhoCompleto);

            $certificado->senha = Crypt::encryptString($dados['senha']);
            $certificado->titular = $metadados['titular'];
            $certificado->numero_serie = $metadados['numero_serie'];
            $certificado->cpf_cnpj = $metadados['cpf_cnpj'];
            $certificado->emitido_em = $metadados['emitido_em'];
            $certificado->vence_em = $metadados['vence_em'];
        }

        if (! empty($dados['nome_certificado'])) {
            $certificado->nome_certificado = $dados['nome_certificado'];
        }

        if (! $certificado->nome_certificado || ! $certificado->cert_path) {
            return null;
        }

        $certificado->save();

        return $certificado;
    }

    public function validarCertificado(string $caminhoArquivo, string $senha): bool
    {
        try {
            $this->converterPfxParaPem($caminhoArquivo, $senha);

            return true;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    public function getCaminhoAbsoluto(CertificadoDigital $certificado): string
    {
        return Storage::disk('local')->path($certificado->cert_path);
    }

    private function converterPfxParaPem(string $caminhoArquivo, string $senha): array
    {
        $conteudo = file_get_contents($caminhoArquivo);
        $certificados = [];

        if (! openssl_pkcs12_read($conteudo, $certificados, $senha)) {
            throw new \InvalidArgumentException('Senha do certificado inválida ou arquivo corrompido.');
        }

        $pem = trim($certificados['cert']) . "\n" . trim($certificados['pkey']);

        if (! empty($certificados['extracerts'])) {
            foreach ((array) $certificados['extracerts'] as $extraCert) {
                $pem .= "\n" . trim($extraCert);
            }
        }

        $metadados = $this->parseMetadadosCertificado($certificados['cert']);
        $metadados['pem'] = $pem . "\n";

        return $metadados;
    }

    private function extrairMetadadosDoPem(string $caminhoArquivo): array
    {
        $conteudo = file_get_contents($caminhoArquivo);
        $certificado = openssl_x509_read($conteudo);

        if (! $certificado) {
            throw new \InvalidArgumentException('Arquivo PEM inválido ou corrompido.');
        }

        return $this->parseMetadadosCertificado($certificado);
    }

    private function parseMetadadosCertificado($certificado): array
    {
        $dados = openssl_x509_parse($certificado);

        $titular = $dados['subject']['CN'] ?? null;
        $numeroSerie = isset($dados['serialNumberHex'])
            ? strtoupper($dados['serialNumberHex'])
            : (isset($dados['serialNumber']) ? strtoupper(dechex($dados['serialNumber'])) : null);

        $cpfCnpj = null;
        if (isset($dados['subject']['serialNumber'])) {
            $cpfCnpj = preg_replace('/\D/', '', $dados['subject']['serialNumber']);
        }

        return [
            'titular' => $titular,
            'numero_serie' => $numeroSerie,
            'cpf_cnpj' => $cpfCnpj,
            'emitido_em' => isset($dados['validFrom_time_t'])
                ? date('Y-m-d H:i:s', $dados['validFrom_time_t'])
                : null,
            'vence_em' => isset($dados['validTo_time_t'])
                ? date('Y-m-d H:i:s', $dados['validTo_time_t'])
                : null,
        ];
    }
}
