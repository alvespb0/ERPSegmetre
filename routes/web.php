<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\CentroCustoController;
use App\Http\Controllers\CategoriaFinanceiraController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    Route::prefix('erp')->name('erp.')->group(function () {
        Route::view('accounts-receivable', 'erp.accounts-receivable')->name('accounts-receivable');
        Route::view('accounts-payable', 'erp.accounts-payable')->name('accounts-payable');
    });

    Route::view('profile', 'profile')->name('profile');
});

Route::middleware(['auth', 'verified'])->controller(EntidadeController::class)->group(function(){
    Route::get('erp/entidades/nova', 'showCreateView')->name('erp.entidades.create');
    Route::get('erp/entidades', 'showListView')->name('erp.entidades.index');
    Route::get('erp/entidades/editar/{idEnc}', 'showEditView')->name('erp.entidades.update');
});

Route::middleware(['auth', 'verified'])->controller(CentroCustoController::class)->group(function(){
    Route::get('erp/centro-custo/nova', 'showCreateView')->name('erp.centro-custo.create');
    Route::get('erp/centro-custo', 'showListView')->name('erp.centro-custo.index');
    Route::get('erp/centro-custo/editar/{idEnc}', 'showEditView')->name('erp.centro-custo.update');
});

Route::middleware(['auth', 'verified'])->controller(CategoriaFinanceiraController::class)->group(function(){
    Route::get('erp/categoria-financeira/nova', 'showCreateView')->name('erp.categoria-financeira.create');
    Route::get('erp/categoria-financeira', 'showListView')->name('erp.categoria-financeira.index');
    Route::get('erp/categoria-financeira/editar/{idEnc}', 'showEditView')->name('erp.categoria-financeira.update');
});

Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
