<?php
namespace App\Helpers;

class LinhaDigitavelFormatter
{
    public static function linhaDigitavelParaCodigoBarras(string $linhaDigitavel): string
    {
        $linha = preg_replace('/\D/', '', $linhaDigitavel);

        if (strlen($linha) !== 47) {
            throw new \InvalidArgumentException(
                'A linha digitável deve possuir 47 dígitos.'
            );
        }

        return
            substr($linha, 0, 4) .
            substr($linha, 32, 1) .
            substr($linha, 33, 14) .
            substr($linha, 4, 5) .
            substr($linha, 10, 10) .
            substr($linha, 21, 10);
    }
}

?>