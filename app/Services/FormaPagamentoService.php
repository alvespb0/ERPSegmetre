<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\FormaPagamento;

class FormaPagamentoService
{
    public function store(array $dados){
        return FormaPagamento::create([
            'nome' => $dados['nome'],
        ]);
    }

    public function update(array $dados, $id){
        $formaPagamento = FormaPagamento::findOrFail($id);

        return $formaPagamento->update([
            'nome' => $dados['nome'],
        ]);
    }

    public function show(){
        return FormaPagamento::orderBy('nome', 'asc')->get();
    }

    public function destroy($id){
        $formaPagamento = FormaPagamento::findOrFail($id);

        return $formaPagamento->delete();
    }

    public function showTrashed(){
        return FormaPagamento::orderBy('nome', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return FormaPagamento::withTrashed()->find($id)->restore();
    }

}