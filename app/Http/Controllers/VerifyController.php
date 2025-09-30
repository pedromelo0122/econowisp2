<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Throwable;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class VerifyController extends Controller
{
    private function client(): Client
    {
        return new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    /**
     * Inicia el envío del OTP
     * channel: sms | whatsapp | call (por defecto sms)
     * body: { "phone": "+1809XXXXXXX", "channel": "sms" }
     */
    public function start(Request $request)
    {
        $request->validate([
            'phone'   => ['required', 'string'],        // En E.164: +1809...
            'channel' => ['nullable', 'in:sms,whatsapp,call'],
        ]);

        $phone   = trim($request->input('phone'));
        $channel = $request->input('channel', 'sms');

        // Evitar costo si el usuario ya está verificado
        if (Schema::hasColumn('users', 'phone_verified_at')) {
            $u = User::where('phone', $phone)->first();
            if ($u && $u->phone_verified_at) {
                return response()->json([
                    'already_verified' => true,
                    'status' => 'approved',
                    'to' => $phone,
                ]);
            }
        }

        try {
            $verify = $this->client()->verify->v2
                ->services(env('TWILIO_VERIFY_SERVICE_SID'))
                ->verifications
                ->create($phone, $channel);

            return response()->json([
                'status'  => $verify->status,                 // "pending"
                'to'      => $verify->to ?? $phone,
                'channel' => $verify->channel ?? $channel,
                'sid'     => $verify->sid,                    // VE...
            ]);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Verifica el código recibido
     * body (OPCIONES):
     * - { "verification_sid": "VE...", "code": "123456" }
     * - { "phone": "+1809XXXXXXX", "code": "123456" }
     */
    public function check(Request $request)
    {
        $request->validate([
            'code'             => ['required', 'string'],
            'verification_sid' => ['nullable', 'string'],
            'phone'            => ['nullable', 'string'],
        ]);

        if (!$request->filled('verification_sid') && !$request->filled('phone')) {
            return response()->json(['error' => 'Envia verification_sid (VE...) o phone (+E.164)'], 400);
        }

        $params = ['code' => $request->input('code')];

        if ($request->filled('verification_sid')) {
            // Usar el SID de la verificación (VE...)
            $params['verificationSid'] = $request->input('verification_sid');
        } else {
            // Fallback: usar el número en E.164
            $params['to'] = trim($request->input('phone'));
        }

        try {
            $result = $this->client()->verify->v2
                ->services(env('TWILIO_VERIFY_SERVICE_SID'))
                ->verificationChecks
                ->create($params);

            // Si el código es correcto, guardar la verificación en la BD
            if ($result->valid && $result->status === 'approved') {
                $confirmedPhone = $result->to ?? ($params['to'] ?? null);

                if ($confirmedPhone && Schema::hasColumn('users', 'phone_verified_at')) {
                    // Ajusta el campo telefónico si en tu tabla no se llama 'phone'
                    $user = User::where('phone', $confirmedPhone)->first();

                    if ($user && !$user->phone_verified_at) {
                        $user->phone_verified_at = now();
                        $user->save();
                    }
                }
            }

            return response()->json([
                'status' => $result->status,  // "approved" si ok
                'valid'  => $result->valid,   // true/false
            ]);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
