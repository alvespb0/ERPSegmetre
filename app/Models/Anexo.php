<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends BaseModel
{
    protected $table = 'anexos';

    protected $fillable = [
        'descricao',
        'path',
        'tipo',
        'empresa_parametro_id',
    ];

    /**
     * Relação polimórfica
     */
    public function anexavel()
    {
        return $this->morphTo();
    }
}
