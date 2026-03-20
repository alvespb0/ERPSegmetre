<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoriaFinanceiraController extends Controller
{
    public function showCreateView(){
        return view('erp.categoria-financeira.create');
    }

}
