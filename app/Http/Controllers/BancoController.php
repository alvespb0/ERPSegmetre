<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class BancoController extends Controller
{
    public function showCreateView(){
        return view('erp.banco.create');
    }

    public function showListView(){
        return view('erp.banco.index');
    }

    public function showEditView($idEnc){
        $id = Crypt::decrypt($idEnc);

        return view('erp.banco.edit', ['id' => $id]);
    }

}
