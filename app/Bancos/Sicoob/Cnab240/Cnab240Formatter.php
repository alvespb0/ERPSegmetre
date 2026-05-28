<?php
namespace App\Bancos\Sicoob\Cnab240;

use Carbon\Carbon;

/**
 * CLASSE HELPER PARA GERAÇÃO DA REMESSA EM PADRÃO CNAB 240
 */
class Cnab240Formatter
{
    public static function alfa($valor, $tamanho){
        $valor = mb_strtoupper(trim($valor));

        $valor = substr($valor, 0, $tamanho);

        return str_pad(
            $valor,
            $tamanho,
            ' ',
            STR_PAD_RIGHT
        );
    }

    public static function numerico($valor, $tamanho){
        return str_pad(
            preg_replace('/\D/', '', $valor),
            $tamanho,
            '0',
            STR_PAD_LEFT
        );
    }

    public static function valor($valor, $tamanho = 15){
        $valor = number_format(
            (float) $valor,
            2,
            '',
            ''
        );

        return self::numerico($valor, $tamanho);
    }

    public static function data($data): string{
        if (!$data) {
            return '00000000';
        }

        return Carbon::parse($data)->format('dmY');
    }
}
