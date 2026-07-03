<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->needsTwoFactorSetup()) {
            return redirect()->route('two-factor.setup');
        }

        if ($request->session()->get('two_factor_passed')) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return view('auth.two-factor.challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->needsTwoFactorSetup()) {
            return redirect()->route('two-factor.setup');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $secret = $user->twoFactorSecret();

        if (! $secret || (new Google2FA())->verifyKey($secret, $request->code) === false) {
            return back()->withErrors(['code' => 'Código inválido. Tente novamente.']);
        }

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->put('two_factor_passed', true);

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
