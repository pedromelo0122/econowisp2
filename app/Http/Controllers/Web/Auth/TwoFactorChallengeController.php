<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Web\Front\FrontController;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;

class TwoFactorChallengeController extends FrontController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * (Opcional) Mostrar el formulario de challenge si necesitas una ruta GET.
     * Si ya tienes tu vista, puedes mantenerla; esto es un respaldo.
     */
    public function show(Request $request)
    {
        if (!session()->has('twoFactorUserId') || !session()->has('twoFactorMethodValue')) {
            return redirect(urlGen()->signIn())->withErrors(['error' => 'Inicia sesión para continuar.']);
        }
        // Si ya tienes una vista existente, úsala.
        // return view('auth.twofactor.challenge');
        // Respaldo minimalista (si no tienes vista):
        return response()->view('auth.otp', [
            // Puedes reutilizar la vista del OTP que ya te pasé en el paso anterior
        ]);
    }

    /**
     * Verifica el código con Twilio Verify y cierra el login.
     */
    public function verify(Request $request): RedirectResponse
    {
        // Validación simple del código
        $request->validate([
            'code' => ['required','digits_between:4,8'],
        ]);

        $userId = session('twoFactorUserId');
        $phone  = session('twoFactorMethodValue'); // E.164

        if (!$userId || !$phone) {
            return redirect(urlGen()->signIn())->withErrors(['error' => 'Sesión de verificación inválida.']);
        }

        // Twilio Verify check
        try {
            $twilio = new Client(
                (string) config('services.twilio.account_sid'),
                (string) config('services.twilio.auth_token')
            );

            $check = $twilio->verify->v2
                ->services((string) config('services.twilio.verify_sid'))
                ->verificationChecks
                ->create([
                    'to'   => $phone,
                    'code' => $request->input('code'),
                ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => 'Error verificando el código: ' . $e->getMessage()]);
        }

        if (($check->status ?? null) !== 'approved') {
            return back()->withErrors(['code' => 'Código inválido o expirado.']);
        }

        // Código correcto: iniciamos sesión y limpiamos la sesión temporal
        Auth::loginUsingId($userId);
        session()->forget(['twoFactorUserId', 'twoFactorMethodValue']);
        session()->put('twoFactorAuthenticated', true);

        // Redirección final (igual que en LoginController)
        $isUrlFromAdminArea = str_contains(url()->previous(), urlGen()->adminUrl());
        $redirectTo = $isUrlFromAdminArea ? urlGen()->adminUrl() : urlGen()->accountOverview();

        return redirect()->intended($redirectTo)->with('status', 'Verificación completada ✅');
    }
}
