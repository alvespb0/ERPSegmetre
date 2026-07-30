<?php
namespace App\Helpers;

use Illuminate\Support\Str;

class IdempotencyKey
{
    public static function make(string $cooperativa, string $conta): string
    {
        return sprintf(
            '%s-%s-%s',
            $cooperativa,
            $conta,
            Str::uuid()
        );
    }
}
?>