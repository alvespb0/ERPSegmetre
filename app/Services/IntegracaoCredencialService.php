<?php

namespace App\Services;

use App\Models\IntegracaoCredencial;
use Illuminate\Support\Facades\Crypt;

class IntegracaoCredencialService
{
    public function sync(int $integracaoId, array $dados): IntegracaoCredencial
    {
        $credencial = IntegracaoCredencial::firstOrNew([
            'integracao_id' => $integracaoId,
        ]);

        $credencial->username = $dados['username'] ?? null;
        $credencial->client_id = $dados['client_id'] ?? null;
        $credencial->access_token = $dados['access_token'] ?? null;
        $credencial->refresh_token = $dados['refresh_token'] ?? null;
        $credencial->token_expires_at = $dados['token_expires_at'] ?? null;
        $credencial->certificado_digital_id = $dados['certificado_digital_id'] ?? null;

        if (! empty($dados['password'])) {
            $credencial->password_enc = Crypt::encryptString($dados['password']);
        }

        if (! empty($dados['client_secret'])) {
            $credencial->client_secret_enc = Crypt::encryptString($dados['client_secret']);
        }

        $credencial->save();

        return $credencial;
    }

    public function update($credencialId, $dados){
        $credenciais = IntegracaoCredencial::findOrFail($credencialId);

        $credenciais->update($dados);

        return $credenciais;
    }
}
