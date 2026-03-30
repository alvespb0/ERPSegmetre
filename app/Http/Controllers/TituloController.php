<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TituloController extends Controller
{
    public function showCreateViewReceita(){
        return view('erp.titulo.conta-receber.create');
    }

    public function showListViewReceita(){
        return view('erp.titulo.conta-receber.index');
    }

    public function showCreateViewDespesa(){
        return view('erp.titulo.conta-pagar.create');
    }

    public function showListViewDespesa(){
        return view('erp.titulo.conta-pagar.index');
    }
}
