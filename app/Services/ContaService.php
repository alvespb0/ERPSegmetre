<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Conta;

class ContaService
{
    public function store(array $dados){
        return Conta::create([
            'banco_id' => $dados['banco_id'],
            'tipo_conta_id' => $dados['tipo_conta_id'],
            'nome' => $dados['nome'],
            'modalidade' => $dados['modalidade'],
            'agencia' => $dados['agencia'] ?? null,
            'conta' => $dados['conta'] ?? null,
        ]);
    }

    public function update(array $dados, $id){
        $conta = Conta::findOrFail($id);

        return $conta->update([
            'banco_id' => $dados['banco_id'],
            'tipo_conta_id' => $dados['tipo_conta_id'],
            'nome' => $dados['nome'],
            'modalidade' => $dados['modalidade'],
            'agencia' => $dados['agencia'] ?? null,
            'conta' => $dados['conta'] ?? null,
        ]);
    }

    public function show(){
        return Conta::orderBy('nome', 'asc')->get();
    }

    public function destroy($id){
        $conta = Conta::findOrFail($id);

        return $conta->delete();
    }

    public function showTrashed(){
        return Conta::orderBy('nome', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return Conta::withTrashed()->find($id)->restore();
    }
}