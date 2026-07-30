<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DDAController extends Controller
{
    public function showListView(){
        return view('erp.dda.index');
    }
}
