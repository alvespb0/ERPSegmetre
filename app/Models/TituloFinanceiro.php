<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TituloFinanceiro extends BaseModel
{
    protected $table = 'titulo_financeiro';

    use SoftDeletes;

    protected $fillable = [
        'centro_custo_id', # nullable
        'categoria_financeira_id', # nullable
        'entidade_id',
        'descricao',
        'observacoes', # nullable
        'numero_nf', # nullable
        'valor_total',
        'data_emissao',
        'tipo', # enum [pagar, receber]
        'status', # enum [aberto, parcial, pago, cancelado] 
    ];

    public function centroCusto(){
        return $this->belongsTo(CentroCusto::class, 'centro_custo_id');
    }

    public function categoriaFinanceira(){
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_financeira_id');
    }

    public function entidade(){
        return $this->belongsTo(Entidade::class, 'entidade_id');
    }
    
    public function parcelas(){
        return $this->hasMany(Parcela::class);
    }
    
    public function anexos(){
        return $this->morphMany(Anexo::class, 'anexavel');
    }

    public function getStatusCalculadoAttribute(){
        if ($this->status === 'cancelado') {
            return 'cancelado';
        }

        $total = $this->valor_total;
        $pago = $this->parcelas->sum->valor_pago;

        if ($pago >= $total) {
            return 'pago';
        }

        if ($pago > 0) {
            return 'parcial';
        }

        $temAtrasado = $this->parcelas->contains(function ($parcela) {
            return $parcela->status_calculado === 'atrasado';
        });

        if ($temAtrasado) {
            return 'atrasado';
        }

        return 'aberto';
    }

    public function getValorPagoAttribute(){
        return $this->parcelas->sum->valor_pago;
    }

    public function getSaldoDevedorAttribute(){
        if ($this->status === 'cancelado') {
            return 0;
        }

        return max($this->valor_total - $this->valor_pago, 0);
    }

    public function getParcelasFaltantesAttribute(){
        return $this->parcelas
            ->filter(function ($parcela) {
                return $parcela->status_calculado !== 'pago' && $parcela->status_calculado !== 'cancelado';
            })
            ->count();
    }

}
