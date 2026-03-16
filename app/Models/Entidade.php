<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidade extends Model
{
    protected $table = 'entidade';

    use SoftDeletes;

    protected $fillable = [
        'razao_social',
        'nome_fantasia', # nullable
        'cpf_cnpj', # unique
        'tipo', # enum [pf, pj]
    ];

    public function titulos(){
        return $this->hasMany(TituloFinanceiro::class);
    }

    public function contatos(){
        return $this->hasMany(Contato::class);
    }
}
