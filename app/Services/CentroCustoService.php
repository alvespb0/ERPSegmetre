<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\CentroCusto;

class CentroCustoService
{
    public function store(array $dados){
        return CentroCusto::create([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
        ]);
    }

    public function update(array $dados, $id){
        $centroCusto = CentroCusto::findOrFail($id);

        return $centroCusto->update([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
        ]);
    }

    public function show(){
        return CentroCusto::orderBy('nome', 'asc')->get();
    }

    public function destroy($id){
        $centroCusto = CentroCusto::findOrFail($id);

        return $centroCusto->delete();
    }

    public function showTrashed(){
        return CentroCusto::orderBy('nome', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return CentroCusto::withTrashed()->find($id)->restore();
    }

}