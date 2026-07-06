<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpresaParametro extends BaseModel
{
    use SoftDeletes;

    protected $table = 'empresa_parametro';

    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'inscricao_municipal',
        'cnae_principal',
        'cep',
        'logradouro',
        'bairro',
        'numero',
        'complemento',
        'cidade',
        'uf',
        'telefone',
        'email_financeiro',
        'logo_path',
    ];

    public function certificadoDigital()
    {
        return $this->hasOne(CertificadoDigital::class, 'empresa_parametro_id');
    }

    public function integracoes()
    {
        return $this->hasMany(Integracao::class, 'empresa_parametro_id');
    }
}
