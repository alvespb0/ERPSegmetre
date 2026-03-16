<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcela extends Model
{
    protected $table = 'parcelas';

    use SoftDeletes;

    protected $fillable = [
        'titulo_financeiro_id',
        'numero_parcela',
        'valor',
        'data_vencimento',
        'status', # enum [aberto, pago, atrasado, parcial]
    ];

    public function titulo(){
        return $this->belongsTo(TituloFinanceiro::class, 'titulo_financeiro_id');
    }

    public function movimentacoes(){
        return $this->hasMany(Movimentacao::class);
    }
}
