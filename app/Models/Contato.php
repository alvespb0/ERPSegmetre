<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contato extends BaseModel
{
    protected $table = 'contato';

    use HasFactory;

    protected $fillable = [
        'entidade_id',
        'telefone',
        'email'
    ];

    public function entidade(){
        return $this->belongsTo(Entidade::class, 'entidade_id');
    }
}
