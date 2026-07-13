<?php

namespace App\Traits;

use App\Helpers\Empresa;
use App\Scopes\EmpresaScope;

trait BelongsToEmpresa
{
    protected static function bootBelongsToEmpresa(): void
    {
        \Log::debug([
            'Teste de trait de multi tentant, chamou a função'
        ]);

        static::addGlobalScope(new EmpresaScope);

        static::creating(function ($model) {
        \Log::debug([
            'creating disparou',
            'empresa_parametro_id' => $model->empresa_parametro_id,
            'session' => session('empresa_parametro_id'),
            'has_session' => session()->has('empresa_parametro_id'),
        ]);

            if (is_null($model->empresa_parametro_id) && session()->has('empresa_parametro_id')) {
                \Log::debug([
                    'Teste de trait de multi tentant, entrou no if'
                ]);
                $model->empresa_parametro_id = Empresa::id();
            }

        });
    }
}