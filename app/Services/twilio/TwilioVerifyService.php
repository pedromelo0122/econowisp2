
<?php

namespace App\Services\Twilio;

use Twilio\Rest\Client;

class TwilioVerifyService
{
        private Client $client;

       private string $serviceSid;

        private ?string $debugTo;

        public function __construct(?Client $client = null)
        {
                $accountSid = config('twilio-notification-channel.account_sid');
                $authToken = config('twilio-notification-channel.auth_token');
                $this->serviceSid = config('twilio-notification-channel.verify_service_sid') ?? env('TWILIO_VERIFY_SERVICE_SID');
                $this->debugTo = config('twilio-notification-channel.debug_to');

                if (empty($accountSid) || empty($authToken) || empty($this->serviceSid)) {
                        throw new \RuntimeException('Twilio Verify is not properly configured.');
                }

                $this->client = $client ?? new Client($accountSid, $authToken);
        }

        public function sendVerification(string $phoneNumber, string $code, ?string $locale = null): void
        {
                $destination = $this->debugTo ?? $phoneNumber;
                $destination = setPhoneSign($destination, 'twilio');

                $payload = [
                        'channel'    => 'sms',
                        'customCode' => $code,
               ];

                if (!empty($locale)) {
                        $payload['locale'] = str_replace('_', '-', $locale);
                }

                $this->client->verify->v2
                        ->services($this->serviceSid)
                        ->verifications
                        ->create($destination, 'sms', $payload);
        }
    
}
