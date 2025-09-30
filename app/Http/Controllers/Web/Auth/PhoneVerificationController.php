<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;

class PhoneVerificationController extends Controller
{
    public function showVerificationForm()
    {
        return view('auth.verify.phone');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        $userId = session('otp_user_id');
        $phone = session('otp_phone');
        if (!$userId || !$phone) {
            return redirect()->route('login')->withErrors(['otp' => 'Sesión expirada, inicia de nuevo.']);
        }

        try {
            $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
            $verification = $twilio->verify->v2->services(env('TWILIO_VERIFY_SERVICE_SID'))
                ->verificationChecks
                ->create(['to' => $phone, 'code' => $request->otp]);
        } catch (\Throwable $e) {
            return back()->withErrors(['otp' => 'Error verificando código: ' . $e->getMessage()]);
        }

        if ($verification->status === 'approved') {
            $user = User::find($userId);
            if ($user) {
                $user->phone_verified_at = now();
                $user->save();
                Auth::login($user);
                session()->forget(['otp_user_id', 'otp_phone']);
                return redirect('/home');
            }
        }

        return back()->withErrors(['otp' => 'Código incorrecto.']);
    }
}