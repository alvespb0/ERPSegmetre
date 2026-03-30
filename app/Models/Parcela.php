<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Parcela extends Model
{
    protected $table = 'parcelas';

    protected $fillable = [
        'titulo_financeiro_id',
        'numero_parcela',
        'valor',
        'data_vencimento',
        'status', # enum ['ativo', 'renegociado', 'cancelado'] STATUS ADMINISTRATIVO, NÃO DE PAGAMENTO, STATUS DE PAGAMENTO É CALCULADO DINAMICAMENTE.
    ];

    public function titulo(){
        return $this->belongsTo(TituloFinanceiro::class, 'titulo_financeiro_id');
    }

    public function movimentacoes(){
        return $this->hasMany(Movimentacao::class);
    }

    public function getStatusCalculadoAttribute(){
        if ($this->status === 'cancelado') {
            return 'cancelado';
        }
        
        $valorPago = $this->valor_pago;

        if (abs($valorPago - $this->valor) < 0.01) {
            return 'pago';
        }
        
        if ($valorPago > 0 && $valorPago < $this->valor) {
            return 'parcial';
        }

        if($this->data_vencimento < now()->startOfDay()){
            return 'atrasado';
        }

        return 'aberto';
    }

    public function getValorPagoAttribute(){
        return $this->movimentacoes->sum('valor_pago');
    }

    public function getSaldoDevedorAttribute(){
        if ($this->status === 'cancelado') {
            return 0;
        }

        $saldo = $this->valor - $this->valor_pago;

        return max($saldo, 0);
    }
}
