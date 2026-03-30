<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Movimentacao;

class MovimentacaoService
{
    public function store(array $dados){
        return Movimentacao::create([
            'forma_pagamento_id' => $dado['forma_pagamento_id'] ?? null,
            'parcela_id' => $dados['parcela_id'],
            'valor_pago' => $dados['valor_pago'],
            'data_pagamento' => $dados['data_pagamento']
        ]);
    }

    public function update(array $dados, $id){
        $movimentacao = Movimentacao::findOrFail($id);

        return $movimentacao->update([
            'forma_pagamento_id' => $dado['banco_id'] ?? null,
            'parcela_id' => $dados['parcela_id'],
            'valor_pago' => $dados['nome'],
            'data_pagamento' => $dados['data_pagamento']
        ]);
    }

    public function show(){
        return Movimentacao::orderBy('data_pagamento', 'asc')->get();
    }

    public function destroy($id){
        $movimentacao = Movimentacao::findOrFail($id);

        return $movimentacao->delete();
    }

    public function showTrashed(){
        return Movimentacao::orderBy('data_pagamento', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return Movimentacao::withTrashed()->find($id)->restore();
    }
}