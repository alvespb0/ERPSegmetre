<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmpresaParametro;

class EmpresaParametroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmpresaParametro::updateOrCreate(
            ['cnpj' => '00000000000000'],
            [
                'razao_social' => 'Empresa Padrão',
                'nome_fantasia' => 'Empresa Padrão',
                'cep' => '00000000',
                'logradouro' => 'Das laranjeiras',
                'bairro' => 'Dos tomates',
                'numero' => '123',
                'complemento' => '456',
                'cidade' => 'Padrao',
                'uf' => 'SC',
                'telefone' => '3561111',
            ]
        );

    }
}
