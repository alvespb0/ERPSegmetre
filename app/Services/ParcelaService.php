<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Parcela;

class ParcelaService
{
    public function store(array $dados){
        return Parcela::create([
            'titulo_financeiro_id' => $dado['titulo_financeiro_id'] ?? null,
            'numero_parcela' => $dados['numero_parcela'],
            'valor' => $dados['valor'],
            'data_vencimento' => $dados['data_vencimento'],
            'status' => $dados['status']
        ]);
    }

    public function update(array $dados, $id){
        $parcela = Parcela::findOrFail($id);

        return $parcela->update([
            'titulo_financeiro_id' => $dado['titulo_financeiro_id'] ?? null,
            'numero_parcela' => $dados['numero_parcela'],
            'valor' => $dados['valor'],
            'data_vencimento' => $dados['data_vencimento'],
            'status' => $dados['status']
        ]);
    }

    public function show(){
        return Parcela::orderBy('data_vencimento', 'asc')->get();
    }

    public function destroy($id){
        $parcela = Parcela::findOrFail($id);

        return $parcela->delete();
    }

    public function showTrashed(){
        return Parcela::orderBy('data_vencimento', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return Parcela::withTrashed()->find($id)->restore();
    }
}