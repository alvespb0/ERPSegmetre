<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Contato;

class ContatoService
{
    public function store(array $dados){
        return Contato::create([
            'entidade_id' => $dados['entidade_id'],
            'telefone' => $dados['telefone'],
            'email' => $dados['email'],
        ]);
    }

    public function updateOrCreate(array $dados, $id){
        return Contato::updateOrCreate(
            ['id' => $id],
            [
                'telefone' => $dados['telefone'],
                'email' => $dados['email'],
            ]
        );
    }

    public function show(){
        return Contato::orderBy('entidade_id', 'asc')->get();
    }

    public function destroy($id){
        $contato = Contato::findOrFail($id);

        return $contato->delete();
    }
}

?>