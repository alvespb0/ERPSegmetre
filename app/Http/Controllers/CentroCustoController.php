<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CentroCustoController extends Controller
{
    public function showCreateView(){
        return view('erp.centro-custo.create');
    }
}
