<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\CentroCustoController;
use App\Http\Controllers\CategoriaFinanceiraController;
use App\Http\Controllers\FormaPagamentoController;
use App\Http\Controllers\BancoController;
use App\Http\Controllers\TipoContaController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\TituloController;
use App\Http\Controllers\EmpresaParametroController;
use App\Http\Controllers\IntegracaoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SOCController;

Route::middleware(['auth', 'two.factor'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('', 'dashboard')->name('dashboard');

    Route::get('perfil', [ProfileController::class, 'show'])->name('perfil');

    Route::prefix('erp')->name('erp.')->group(function () {
        Route::view('accounts-receivable', 'erp.accounts-receivable')->name('accounts-receivable');
        Route::view('accounts-payable', 'erp.accounts-payable')->name('accounts-payable');
    });
});

Route::middleware(['checkUserType:admin,dev', 'two.factor'])->controller(UserController::class)->group(function () {
    Route::get('erp/usuarios/nova', 'showCreateView')->name('erp.usuarios.create');
    Route::post('erp/usuarios', 'store')->name('erp.usuarios.store');
    Route::get('erp/usuarios', 'showListView')->name('erp.usuarios.index');
    Route::get('erp/usuarios/editar/{idEnc}', 'showEditView')->name('erp.usuarios.update');
});

Route::middleware(['checkUserType:admin,dev,cobranca,', 'two.factor'])->controller(EntidadeController::class)->group(function(){
    Route::get('erp/entidades/nova', 'showCreateView')->name('erp.entidades.create');
    Route::get('erp/entidades', 'showListView')->name('erp.entidades.index');
    Route::get('erp/entidades/editar/{idEnc}', 'showEditView')->name('erp.entidades.update');
});

Route::middleware(['checkUserType:admin,dev,cobranca', 'two.factor'])->controller(CentroCustoController::class)->group(function(){
    Route::get('erp/centro-custo/nova', 'showCreateView')->name('erp.centro-custo.create');
    Route::get('erp/centro-custo', 'showListView')->name('erp.centro-custo.index');
    Route::get('erp/centro-custo/editar/{idEnc}', 'showEditView')->name('erp.centro-custo.update');
});

Route::middleware(['checkUserType:admin,dev,cobranca', 'two.factor'])->controller(CategoriaFinanceiraController::class)->group(function(){
    Route::get('erp/categoria-financeira/nova', 'showCreateView')->name('erp.categoria-financeira.create');
    Route::get('erp/categoria-financeira', 'showListView')->name('erp.categoria-financeira.index');
    Route::get('erp/categoria-financeira/editar/{idEnc}', 'showEditView')->name('erp.categoria-financeira.update');
});
Route::middleware(['checkUserType:admin,dev,cobranca', 'two.factor'])->controller(CategoriaFinanceiraController::class)->group(function(){
    Route::get('erp/fluxo-caixa', function(){ return view('erp.fluxo-caixa.index'); })->name('erp.fluxo-caixa');
});

Route::middleware(['checkUserType:admin,dev,cobranca,visualizador', 'two.factor'])->get('erp/relatorios', function () {
    return view('erp.relatorio.index');
})->name('erp.relatorios.index');

Route::middleware(['checkUserType:admin,dev,cobranca', 'two.factor'])->controller(BancoController::class)->group(function(){
    Route::get('erp/banco/nova', 'showCreateView')->name('erp.banco.create');
    Route::get('erp/banco', 'showListView')->name('erp.banco.index');
    Route::get('erp/banco/editar/{idEnc}', 'showEditView')->name('erp.banco.update');
});

Route::middleware(['checkUserType:admin,dev,cobranca', 'two.factor'])->controller(FormaPagamentoController::class)->group(function(){
    Route::get('erp/forma-pagamento/nova', 'showCreateView')->name('erp.forma-pagamento.create');
    Route::get('erp/forma-pagamento', 'showListView')->name('erp.forma-pagamento.index');
    Route::get('erp/forma-pagamento/editar/{idEnc}', 'showEditView')->name('erp.forma-pagamento.update');
});

Route::middleware(['checkUserType:admin,dev,cobranca', 'two.factor'])->controller(TipoContaController::class)->group(function(){
    Route::get('erp/tipo-conta/nova', 'showCreateView')->name('erp.tipo-conta.create');
    Route::get('erp/tipo-conta', 'showListView')->name('erp.tipo-conta.index');
    Route::get('erp/tipo-conta/editar/{idEnc}', 'showEditView')->name('erp.tipo-conta.update');
});

Route::middleware(['checkUserType:admin,dev,cobranca', 'two.factor'])->controller(ContaController::class)->group(function(){
    Route::get('erp/conta/nova', 'showCreateView')->name('erp.conta.create');
    Route::get('erp/conta', 'showListView')->name('erp.conta.index');
    Route::get('erp/conta/editar/{idEnc}', 'showEditView')->name('erp.conta.update');
});

Route::middleware(['checkUserType:dev', 'two.factor'])->controller(EmpresaParametroController::class)->group(function(){
    Route::get('erp/dev/empresa-parametro', 'showIndexView')->name('erp.dev.empresa-parametro.index');
    Route::get('erp/empresa-parametro/nova', 'showCreateView')->name('erp.empresa-parametro.create');
    Route::get('erp/empresa-parametro/editar/{idEnc}', 'showEditView')->name('erp.empresa-parametro.update');
});

Route::middleware(['checkUserType:dev', 'two.factor'])->controller(IntegracaoController::class)->group(function(){
    Route::get('erp/dev/integracoes', 'showListView')->name('erp.dev.integracoes.index');
    Route::get('erp/dev/integracoes/nova', 'showCreateView')->name('erp.dev.integracoes.create');
    Route::get('erp/dev/integracoes/editar/{idEnc}', 'showEditView')->name('erp.dev.integracoes.update');
});

Route::middleware(['auth', 'two.factor'])->controller(TituloController::class)->group(function(){
    Route::get('erp/titulo/receita/nova', 'showCreateViewReceita')->name('erp.receita.create');
    Route::get('erp/titulo/receita', 'showListViewReceita')->middleware(['checkUserType:admin,dev,cobranca,visualisador'])->name('erp.receita.index');
    Route::get('erp/titulo/despesa/nova', 'showCreateViewDespesa')->name('erp.despesa.create');
    Route::get('erp/titulo/despesa', 'showListViewDespesa')->middleware(['checkUserType:admin,dev,cobranca,pagador'])->name('erp.despesa.index');
});

Route::middleware(['checkUserType:admin,dev', 'two.factor'])->controller(SOCController::class)->group(function(){
    Route::get('erp/SOC/valorizacoes', 'showListView')->name('erp.receita.valorizacao.soc');
});

Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
