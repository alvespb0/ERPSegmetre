<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TipoConta extends BaseModel
{
    protected $table = 'tipo_conta';

    use SoftDeletes;

    protected $fillable = [
        'descricao'
    ];

    public function contas(){
        return $this->hasMany(Conta::class);
    }

}
