<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CertificadoDigital extends Model
{
    use SoftDeletes;

    protected $table = 'certificados_digitais';

    protected $hidden = [
        'senha',
    ];

    protected $fillable = [
        'empresa_parametro_id',
        'nome_certificado',
        'cert_path',
        'senha',
        'cpf_cnpj',
        'titular',
        'numero_serie',
        'emitido_em',
        'vence_em',
    ];

    protected $casts = [
        'emitido_em' => 'datetime',
        'vence_em' => 'datetime',
    ];

    public function empresaParametro()
    {
        return $this->belongsTo(EmpresaParametro::class, 'empresa_parametro_id');
    }
}
