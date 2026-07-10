<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Integracao;
use App\Models\EmpresaParametro;

class SicoobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa = EmpresaParametro::first();

        Integracao::updateOrCreate(
            ['slug' => 'sicoob-producao'],
            [
                'empresa_parametro_id' => $empresa->id,
                'nome' => 'SICOOB-PRODUCAO',
                'provider' => 'App\Bancos\Sicoob\Services\SicoobService',
                'escopo' => 'banco',
                'tecnologia' => 'rest',
                'autenticacao' => 'oauth2',
                'endpoint' => 'https://api.sicoob.com.br/',
                'nativa' => 1
            ]
        );

    }
}
