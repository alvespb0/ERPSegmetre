<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmpresaParametro extends Model
{
    protected $table = 'user_empresa_parametro';

    protected $fillable = [
        'user_id',
        'empresa_parametro_id',
    ];

    /**
     * Usuário relacionado.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parâmetro da empresa relacionado.
     */
    public function empresaParametro()
    {
        return $this->belongsTo(EmpresaParametro::class, 'empresa_parametro_id');
    }
}