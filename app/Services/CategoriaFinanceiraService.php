<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\CategoriaFinanceira;

class CategoriaFinanceiraService
{
    public function store(array $dados){
        return CategoriaFinanceira::create([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'tipo' => $dados['tipo']
        ]);
    }

    public function update(array $dados, $id){
        $categoria = CategoriaFinanceira::findOrFail($id);

        return $categoria->update([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'tipo' => $dados['tipo']
        ]);
    }

    public function show(){
        return CategoriaFinanceira::orderBy('nome', 'asc')->get();
    }

    public function showReceitas(){
        return CategoriaFinanceira::where('tipo', 'receita')->get();
    }

    public function destroy($id){
        $categoria = CategoriaFinanceira::findOrFail($id);

        return $categoria->delete();
    }

    public function showTrashed(){
        return CategoriaFinanceira::orderBy('nome', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return CategoriaFinanceira::withTrashed()->find($id)->restore();
    }

}