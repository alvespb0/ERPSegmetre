<?php

namespace App\Scopes;

use App\Helpers\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EmpresaScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!session()->has('empresa_parametro_id')) {
            return;
        }

        $builder->where(
            $model->getTable() . '.empresa_parametro_id',
            Empresa::id()
        );
    }
}