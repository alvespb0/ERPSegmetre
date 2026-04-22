<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Entidade;

class EntidadeService
{
    public function store(array $dados){
        return Entidade::create([
            'razao_social' => $dados['razao_social'],
            'nome_fantasia' => $dados['nome_fantasia'] ?? null,
            'cpf_cnpj' => $dados['cpf_cnpj'],
            'tipo' => $dados['tipo'],
            'classificacao' => $dados['classificacao']
        ]);
    }

    public function update(array $dados, $id){
        $entidade = Entidade::findOrFail($id);

        return $entidade->update([
            'razao_social' => $dados['razao_social'],
            'nome_fantasia' => $dados['nome_fantasia'] ?? null,
            'cpf_cnpj' => $dados['cpf_cnpj'],
            'tipo' => $dados['tipo'],
            'classficacao' => $dados['classificacao']
        ]);
    }

    public function show(){
        return Entidade::orderBy('razao_social', 'asc')->get();
    }

    public function showClientes(){
        return Entidade::whereIn('classificacao', ['cliente','ambos'])->orderBy('razao_social', 'asc')->get();
    }

    public function showFornecedores(){
        return Entidade::whereIn('classificacao', ['fornecedor','ambos'])->orderBy('razao_social', 'asc')->get();
    }

    public function destroy($id){
        $entidade = Entidade::findOrFail($id);

        return $entidade->delete();
    }

    public function showTrashed(){
        return Entidade::orderBy('razao_social', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return Entidade::withTrashed()->find($id)->restore();
    }
}

?>