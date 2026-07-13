<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmpresaParametro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
        $empresas = EmpresaParametro::query()
            ->orderBy('nome_fantasia')
            ->orderBy('razao_social')
            ->get(['id', 'nome_fantasia', 'razao_social']);

        return view('erp.usuarios.create', compact('empresas'));
    }

    public function showEditView(string $idEnc): View
    {
        $id = Crypt::decrypt($idEnc);

        return view('erp.usuarios.edit', ['id' => $id]);
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tipo' => ['required', Rule::in(['dev', 'admin', 'visualizador', 'pagador', 'cobranca'])],
        ];

        if ($request->user()->isDev() && $request->input('tipo') !== 'dev') {
            $rules['empresa_parametro_ids'] = ['required', 'array', 'min:1'];
            $rules['empresa_parametro_ids.*'] = ['integer', 'exists:empresa_parametro,id'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tipo' => $validated['tipo'],
        ]);

        if ($request->user()->isDev() && $validated['tipo'] !== 'dev') {
            $user->empresas()->sync($validated['empresa_parametro_ids']);
        }

        return redirect()
            ->route('erp.usuarios.index')
            ->with('toast-message', 'Usuário criado com sucesso!');
    }
}
