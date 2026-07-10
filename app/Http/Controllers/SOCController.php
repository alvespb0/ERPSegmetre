<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SOCController extends Controller
{
    public function showListView(){
        return view('erp.Soc.index');
    }

}
