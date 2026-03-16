<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Entidades (somente views / mock)
        Route::view('entidades', 'erp.entidades.index')->name('entidades.index');
        Route::view('entidades/nova', 'erp.entidades.create')->name('entidades.create');
    });

    Route::view('profile', 'profile')->name('profile');
});

Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
