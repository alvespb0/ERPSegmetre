<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorSetupController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->needsTwoFactorSetup()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $google2fa = new Google2FA();

        if (! $user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->two_factor_secret = Crypt::encryptString($secret);
            $user->save();
        } else {
            $secret = $user->twoFactorSecret();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.two-factor.setup', [
            'qrCodeSvg' => $this->qrCodeSvg($qrCodeUrl),
            'secret' => $secret,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->needsTwoFactorSetup()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $secret = $user->twoFactorSecret();

        if ((new Google2FA())->verifyKey($secret, $request->code) === false) {
            return back()->withErrors(['code' => 'Código inválido. Tente novamente.']);
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->put('two_factor_passed', true);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    private function qrCodeSvg(string $url): string
    {
        $writer = new Writer(new ImageRenderer(
            new RendererStyle(192),
            new SvgImageBackEnd()
        ));

        return $writer->writeString($url);
    }
}
