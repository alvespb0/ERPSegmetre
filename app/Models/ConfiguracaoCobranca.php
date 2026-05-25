<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoCobranca extends Model
{
    use SoftDeletes;

    protected $table = 'configuracao_cobranca';

    protected $fillable = [
        'conta_id',
        'empresa_parametro_id',
        'codigo_cedente',
        'carteira',
        'layout_cnab',
        'ambiente',
        'ultimo_numero_remessa',
        'nosso_numero',
    ];

    public function conta(){
        return $this->belongsTo(Conta::class, 'conta_id');
    }

    public function empresaParametro(){
        return $this->belongsTo(EmpresaParametro::class, 'empresa_parametro_id');
    }

}
