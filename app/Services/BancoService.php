<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Banco;

class BancoService
{
    public function store(array $dados){
        return Banco::create([
            'nome' => $dados['nome'],
            'cnpj' => $dados['cnpj'],
        ]);
    }

    public function update(array $dados, $id){
        $banco = Banco::findOrFail($id);

        return $banco->update([
            'nome' => $dados['nome'],
            'cnpj' => $dados['cnpj'],
        ]);
    }

    public function show(){
        return Banco::orderBy('nome', 'asc')->get();
    }

    public function destroy($id){
        $banco = Banco::findOrFail($id);

        return $banco->delete();
    }

    public function showTrashed(){
        return Banco::orderBy('nome', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return Banco::withTrashed()->find($id)->restore();
    }

}