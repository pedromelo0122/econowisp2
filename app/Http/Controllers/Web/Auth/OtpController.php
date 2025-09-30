<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;

class OtpController extends Controller
{
    private function twilio(): Client
    {
        return new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );
    }

    public function form(Request $request)
    {
        // Solo si venimos de un login válido y con teléfono en sesión
        if (!session()->has('pre_auth_user_id') || !session()->has('otp_phone')) {
            return redirect()->route('login')->withErrors(['login' => 'Inicia sesión para continuar.']);
        }
        return view('auth.otp');
    }

    public function resend(Request $request): RedirectResponse
    {
        $phone = session('otp_phone');
        if (!$phone) return redirect()->route('login');

        try {
            $this->twilio()->verify->v2->services(config('services.twilio.verify_sid'))
                ->verifications
                ->create($phone, 'sms'); // sms | whatsapp | call | auto
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => 'No se pudo enviar el código: '.$e->getMessage()]);
        }

        return back()->with('status', 'Código reenviado.');
    }

    public function verify(VerifyOtpRequest $request): RedirectResponse
    {
        $phone = session('otp_phone');
        $userId = session('pre_auth_user_id');
        if (!$phone || !$userId) return redirect()->route('login');

        try {
            $check = $this->twilio()->verify->v2->services(config('services.twilio.verify_sid'))
                ->verificationChecks
                ->create([
                    'to'   => $phone,
                    'code' => $request->validated()['code'],
                ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => 'Error verificando el código: '.$e->getMessage()]);
        }

        if (($check->status ?? null) === 'approved') {
            Auth::loginUsingId($userId);
            session()->forget(['pre_auth_user_id', 'otp_phone']);
            return redirect()->intended('/')->with('status', 'Verificación completada ✅');
        }

        return back()->withErrors(['code' => 'Código inválido o expirado.']);
    }
}
