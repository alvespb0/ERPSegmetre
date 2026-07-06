<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentroCusto extends BaseModel
{
    protected $table = 'centro_custo';

    use SoftDeletes;

    protected $fillable = [
        'nome', # unique
        'descricao', # nullable
    ];

    public function titulos(){
        return $this->hasMany(TituloFinanceiro::class);
    }

}
