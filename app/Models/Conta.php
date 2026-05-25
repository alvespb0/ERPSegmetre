<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conta extends Model
{
    protected $table = 'conta';

    use SoftDeletes;

    protected $fillable = [
        'banco_id',
        'tipo_conta_id',
        'nome', # unique
        'modalidade', # enum [pj, pf]
        'agencia', # nullable
        'conta', # nullable
    ];

    public function banco(){
        return $this->belongsTo(Banco::class, 'banco_id');
    }

    public function tipoConta(){
        return $this->belongsTo(TipoConta::class, 'tipo_conta_id');
    }

    public function movimentacoes(){
        return $this->hasMany(Movimentacao::class);
    }

    public function configuracaoCobranca(){
        return $this->hasOne(ConfiguracaoCobranca::class);
    }
}
