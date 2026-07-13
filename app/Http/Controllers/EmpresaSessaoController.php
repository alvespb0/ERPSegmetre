<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmpresaSessaoController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'empresa_parametro_id' => ['required', 'integer'],
        ]);

        $user = $request->user();
        $empresaId = (int) $request->input('empresa_parametro_id');

        $hasAccess = $user->empresasDisponiveis()
            ->whereKey($empresaId)
            ->exists();

        if (! $hasAccess) {
            abort(403, 'Você não tem acesso a esta empresa.');
        }

        $request->session()->put('empresa_parametro_id', $empresaId);

        return redirect()->back()->with('toast-message', 'Empresa alterada com sucesso.');
    }
}
