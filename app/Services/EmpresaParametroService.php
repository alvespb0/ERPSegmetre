<?php

namespace App\Services;

use App\Models\EmpresaParametro;

class EmpresaParametroService
{
    public function store(array $dados): EmpresaParametro
    {
        return EmpresaParametro::create($dados);
    }
}
