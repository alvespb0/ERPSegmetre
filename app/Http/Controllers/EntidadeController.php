<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EntidadeController extends Controller
{
    public function showCreateView(){
        return view('erp.entidades.create');
    }

    public function showListView(){
        return view('erp.entidades.index');
    }

    public function showEditView($idEnc){
        $id = Crypt::decrypt($idEnc);

        return view('erp.entidades.edit', ['id' => $id]);
    }
}
