<?php

namespace App\Bancos\Sicoob\Services;

use App\Exceptions\SicoobException;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\BoletoCobranca;
use App\Models\Integracao;

class SicoobPagamentoService
{
    protected $integracao;

    public function __construct(Integracao $integracao){
        $this->integracao = $integracao;
    }

}