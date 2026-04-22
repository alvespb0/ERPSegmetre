<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CentroCustoController extends Controller
{
    public function showCreateView(){
        return view('erp.centro-custo.create');
    }

    public function showListView(){
        return view('erp.centro-custo.index');
    }

    public function showEditView($idEnc){
        $id = Crypt::decrypt($idEnc);

        return view('erp.centro-custo.edit', ['id' => $id]);
    }

}
