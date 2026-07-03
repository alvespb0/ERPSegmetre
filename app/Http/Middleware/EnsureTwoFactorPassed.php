<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorPassed
{
    private const EXEMPT_ROUTES = [
        'two-factor.setup',
        'two-factor.setup.store',
        'two-factor.challenge',
        'two-factor.challenge.store',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || in_array($request->route()?->getName(), self::EXEMPT_ROUTES, true)) {
            return $next($request);
        }

        if ($user->needsTwoFactorSetup()) {
            return redirect()->route('two-factor.setup');
        }

        if (! $request->session()->get('two_factor_passed')) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
