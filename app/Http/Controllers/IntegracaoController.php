<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;

class IntegracaoController extends Controller
{
    public function showListView()
    {
        return view('erp.integracao.index');
    }

    public function showCreateView()
    {
        return view('erp.integracao.create');
    }

    public function showEditView($idEnc)
    {
        $id = Crypt::decrypt($idEnc);

        return view('erp.integracao.edit', ['id' => $id]);
    }
}
