<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function showListView(): View
    {
        return view('erp.usuarios.index');
    }

    public function showCreateView(): View
    {
        return view('erp.usuarios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tipo' => ['required', Rule::in(['dev', 'admin', 'visualizador', 'pagador', 'cobranca'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => $request->tipo,
        ]);

        return redirect()
            ->route('erp.usuarios.index')
            ->with('toast-message', 'Usuário criado com sucesso!');
    }
}
