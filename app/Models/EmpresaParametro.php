<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmpresaParametro extends Model
{
    use SoftDeletes, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions{
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->useLogName(class_basename($this));
    }

    public function certificadoDigital()
    {
        return $this->hasOne(CertificadoDigital::class, 'empresa_parametro_id');
    }

    public function integracoes()
    {
        return $this->hasMany(Integracao::class, 'empresa_parametro_id');
    }
}
