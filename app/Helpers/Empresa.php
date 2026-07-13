<?php
namespace App\Helpers;

class Empresa
{
    public static function id(): int
    {
        return session('empresa_parametro_id');
    }

    public static function atual(): EmpresaParametro
    {
        return EmpresaParametro::findOrFail(self::id());
    }
}

?>