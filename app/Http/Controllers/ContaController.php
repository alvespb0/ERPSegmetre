<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ContaController extends Controller
{
    public function showCreateView(){
        return view('erp.conta.create');
    }

    public function showListView(){
        return view('erp.conta.index');
    }

    public function showEditView($idEnc){
        $id = Crypt::decrypt($idEnc);

        return view('erp.conta.edit', ['id' => $id]);
    }

}
