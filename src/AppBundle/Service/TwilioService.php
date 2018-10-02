<?php

namespace AppBundle\Service;

use Twilio\Rest\Client;

/**
 * Class TwilioService
 */
class TwilioService
{
    protected $sid;

    protected $token;

    protected $from;


    /**
     * @param string $sid
     * @param string $token
     * @param string $from
     */
    public function __construct($sid, $token, $from)
    {
        $this->sid = $sid;
        $this->token = $token;
        $this->from = $from;
    }

    /**
     * @param string $phone
     * @param string $message
     *
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     *
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Rest\Api\V2010\Account\TwilioException
     */
    public function sendSms($phone, $message)
    {
        $client = new Client($this->sid, $this->token);
        $result = $client->messages->create($phone, [
            'from' => $this->from, // From a valid Twilio number
            'body' => $message,
        ]);

        return $result;
    }
}
