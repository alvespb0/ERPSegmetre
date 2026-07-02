<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class FormaPagamentoController extends Controller
{
    public function showCreateView(){
        return view('erp.forma-pagamento.create');
    }

    public function showListView(){
        return view('erp.forma-pagamento.index');
    }

    public function showEditView($idEnc){
        $id = Crypt::decrypt($idEnc);

        return view('erp.forma-pagamento.edit', ['id' => $id]);
    }

}
