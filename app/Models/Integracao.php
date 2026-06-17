<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Integracao extends Model
{
    use SoftDeletes;

    protected $table = 'integracoes';

    protected $fillable = [
        'empresa_parametro_id',
        'nome',
        'slug',
        'provider', # classe responsável por essa integracao
        'descricao',
        'escopo',
        'tecnologia',
        'autenticacao',
        'autenticacao_especifica',
        'endpoint',
        'nativa',
    ];

    protected $casts = [
        'nativa' => 'boolean',
    ];

    public function empresaParametro()
    {
        return $this->belongsTo(EmpresaParametro::class, 'empresa_parametro_id');
    }

    public function credenciais()
    {
        return $this->hasOne(IntegracaoCredencial::class, 'integracao_id');
    }
}
