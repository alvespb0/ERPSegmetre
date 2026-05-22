<?php

namespace App\Http\Controllers;

class EmpresaParametroController extends Controller
{
    public function showCreateView()
    {
        return view('erp.empresa-parametro.create');
    }
}
