<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CategoriaFinanceiraController extends Controller
{
    public function showCreateView(){
        return view('erp.categoria-financeira.create');
    }
    
    public function showListView(){
        return view('erp.categoria-financeira.index');
    }

    public function showEditView($idEnc){
        $id = Crypt::decrypt($idEnc);

        return view('erp.categoria-financeira.edit', ['id' => $id]);
    }

}
