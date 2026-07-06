<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegracaoCredencial extends BaseModel
{
    protected $table = 'integracao_credenciais';

    protected $hidden = [
        'password_enc',
        'client_secret_enc',
        'access_token',
        'refresh_token',
    ];

    protected $fillable = [
        'integracao_id',
        'username',
        'password_enc',
        'client_id',
        'client_secret_enc',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'scope',
        'certificado_digital_id',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    public function integracao()
    {
        return $this->belongsTo(Integracao::class, 'integracao_id');
    }

    public function certificadoDigital()
    {
        return $this->belongsTo(CertificadoDigital::class, 'certificado_digital_id');
    }

    public function getTokenValidoAttribute(): bool{
        return $this->token_expires_at !== null
            && $this->token_expires_at->isFuture();
    }
}
