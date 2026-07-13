<?php

namespace App\Http\Controllers;

use App\Models\EmpresaParametro;
use Illuminate\Support\Facades\Crypt;

class EmpresaParametroController extends Controller
{
    public function showIndexView()
    {
        if (! EmpresaParametro::exists()) {
            return redirect()->route('erp.empresa-parametro.create');
        }

        return view('erp.empresa-parametro.index');
    }

    public function showCreateView()
    {
        return view('erp.empresa-parametro.create');
    }

    public function showEditView($idEnc)
    {
        $id = Crypt::decrypt($idEnc);

        return view('erp.empresa-parametro.edit', ['id' => $id]);
    }
}
