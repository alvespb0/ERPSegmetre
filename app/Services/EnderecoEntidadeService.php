<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\EnderecoEntidade;

class EnderecoEntidadeService
{
    public function store(array $dados){
        return EnderecoEntidade::create([
            'entidade_id' => $dados['entidade_id'],
            'rua' => $dados['rua'],
            'bairro' => $dados['bairro'],
            'numero' => $dados['numero'],
            'cep' => $dados['cep'],
            'cidade' => $dados['cidade'],
            'uf' => $dados['uf'],
            'complemento' => $dados['complemento'] ?? null
        ]);
    }

    public function update(array $dados, $id){
        $endereco = EnderecoEntidade::findOrFail($id);

        return $endereco->update([
            'entidade_id' => $dados['entidade_id'],
            'rua' => $dados['rua'],
            'bairro' => $dados['bairro'],
            'numero' => $dados['numero'],
            'cep' => $dados['cep'],
            'cidade' => $dados['cidade'],
            'uf' => $dados['uf'],
            'complemento' => $dados['complemento'] ?? null
        ]);
    }

    public function show(){
        return EnderecoEntidade::orderBy('cep', 'asc')->get();
    }

    public function destroy($id){
        $endereco = EnderecoEntidade::findOrFail($id);

        return $endereco->delete();
    }
}

?>