<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    protected $table = 'anexos';

    protected $fillable = [
        'descricao',
        'path',
        'tipo',
    ];

    /**
     * Relação polimórfica
     */
    public function anexavel()
    {
        return $this->morphTo();
    }
}
