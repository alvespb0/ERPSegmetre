<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaFinanceira extends Model
{
    protected $table = 'categoria_financeira';

    use SoftDeletes;

    protected $fillable = [
        'nome', # unique
        'descricao', # nullable
        'tipo', # enum [receita, despesa]
    ];

    public function titulos(){
        return $this->hasMany(TituloFinanceiro::class);
    }
}
