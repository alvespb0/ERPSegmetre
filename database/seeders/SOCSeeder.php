<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Integracao;
use App\Models\EmpresaParametro;

class SOCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa = EmpresaParametro::first();

        Integracao::updateOrCreate(
            ['slug' => 'soc-exames-producao'],
            [
                'empresa_parametro_id' => $empresa->id,
                'nome' => 'SOC-EXAMES-PRODUCAO',
                'provider' => 'App\Services\Integracoes\SOC\SOCService',
                'escopo' => 'sistema',
                'tecnologia' => 'rest',
                'autenticacao' => 'outro',
                'endpoint' => 'https://ws1.soc.com.br/WebSoc/exportadados',
                'nativa' => 1
            ]
        );

        Integracao::updateOrCreate(
            ['slug' => 'soc-empresas-producao'],
            [
                'empresa_parametro_id' => $empresa->id,
                'nome' => 'SOC-EMPRESAS-PRODUCAO',
                'provider' => 'App\Services\Integracoes\SOC\SOCService',
                'escopo' => 'sistema',
                'tecnologia' => 'rest',
                'autenticacao' => 'outro',
                'endpoint' => 'https://ws1.soc.com.br/WebSoc/exportadados',
                'nativa' => 1
            ]
        );
    }
}
