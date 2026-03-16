<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TituloFinanceiro extends Model
{
    protected $table = 'titulo_financeiro';

    use SoftDeletes;

    protected $fillable = [
        'centro_custo_id', # nullable
        'categoria_financeira_id', # nullable
        'conta_id', # nullable
        'entidade_id',
        'descricao',
        'observacoes', # nullable
        'numero_nf', # nullable
        'valor_total',
        'data_vencimento',
        'tipo', # enum [pagar, receber]
        'status', # enum [aberto, parcial, pago, cancelado] 
    ];

    public function centroCusto(){
        return $this->belongsTo(CentroCusto::class, 'centro_custo_id');
    }

    public function categoriaFinanceira(){
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_financeira_id');
    }

    public function conta(){
        return $this->belongsTo(Conta::class, 'conta_id');
    }

    public function entidade(){
        return $this->belongsTo(Entidade::class, 'entidade_id');
    }
    
    public function parcelas(){
        return $this->hasMany(Parcela::class);
    }
}
