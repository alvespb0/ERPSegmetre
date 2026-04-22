<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TipoContaController extends Controller
{
    public function showCreateView(){
        return view('erp.tipo-conta.create');
    }

    public function showListView(){
        return view('erp.tipo-conta.index');
    }

    public function showEditView($idEnc){
        $id = Crypt::decrypt($idEnc);

        return view('erp.tipo-conta.edit', ['id' => $id]);
    }
}
