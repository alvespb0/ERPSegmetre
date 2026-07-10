<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidade extends BaseModel
{
    protected $table = 'entidade';

    use SoftDeletes;

    protected $fillable = [
        'razao_social',
        'nome_fantasia', # nullable
        'cpf_cnpj', # unique
        'tipo', # enum [pf, pj]
        'classificacao', #enum [cliente, fornecedor, ambos]
        'dia_vencimento_padrao'
    ];

    public function titulos(){
        return $this->hasMany(TituloFinanceiro::class);
    }

    public function contatos(){
        return $this->hasMany(Contato::class);
    }

    public function enderecos(){
        return $this->hasMany(EnderecoEntidade::class);
    }

    public function integracoesSoc(){
        return $this->hasMany(IntegracaoSocEmpresa::class);
    }
}
