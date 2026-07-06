<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$types): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('mensagem', 'Você precisa estar logado.');
        }

        $user = Auth::user();

        if (!in_array($user->tipo, $types)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
