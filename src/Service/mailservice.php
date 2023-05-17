<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioClient
{
    private $client;

    public function __construct(string $accountSid, string $authToken)
    {
        $this->client = new Client($accountSid, $authToken);
    }

    public function sendSMS(string $to, string $from, string $body)
    {
        $this->client->messages->create($to, [
            'from' => $from,
            'body' => $body
        ]);
    }
}