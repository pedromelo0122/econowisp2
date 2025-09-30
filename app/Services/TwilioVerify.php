<?php

namespace App\Services\Auth;

use Twilio\Rest\Client;

class TwilioVerify
{
    protected Client $client;
    protected string $verifySid;

    public function __construct()
    {
        $sid  = (string) config('services.twilio.account_sid');
        $tok  = (string) config('services.twilio.auth_token');
        $this->verifySid = (string) config('services.twilio.verify_sid');

        $this->client = new Client($sid, $tok);
    }

    /**
     * Envía un código vía Twilio Verify al teléfono dado.
     * IMPORTANTE: el número debe estar en formato E.164 (ej: +18095551234).
     */
    public function send(string $phone, string $channel = 'sms'): array
    {
        $res = $this->client->verify->v2->services($this->verifySid)
            ->verifications
            ->create($phone, $channel);

        return [
            'sid'    => $res->sid ?? null,
            'status' => $res->status ?? null, // "pending" si salió bien
            'to'     => $res->to ?? null,
            'channel'=> $channel,
        ];
    }

    /**
     * Verifica un código recibido por el usuario.
     * Devuelve true si el código es correcto ("approved").
     */
    public function check(string $phone, string $code): bool
    {
        $res = $this->client->verify->v2->services($this->verifySid)
            ->verificationChecks
            ->create([
                'to'   => $phone,
                'code' => $code,
            ]);

        return ($res->status ?? null) === 'approved';
    }
}
