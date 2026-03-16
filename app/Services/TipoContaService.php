<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\TipoConta;

class TipoContaService
{
    public function store(array $dados){
        return TipoConta::create([
            'descricao' => $dados['descricao'],
        ]);
    }

    public function update(array $dados, $id){
        $tipoConta = TipoConta::findOrFail($id);

        return $tipoConta->update([
            'descricao' => $dados['descricao'],
        ]);
    }

    public function show(){
        return TipoConta::orderBy('descricao', 'asc')->get();
    }

    public function destroy($id){
        $tipoConta = TipoConta::findOrFail($id);

        return $tipoConta->delete();
    }

    public function showTrashed(){
        return TipoConta::orderBy('descricao', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return TipoConta::withTrashed()->find($id)->restore();
    }
}