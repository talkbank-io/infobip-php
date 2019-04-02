<?php

namespace TalkBank\Infobip;

use GuzzleHttp\Client as GuzzleClient;

/**
 * API for partners
 *   $client = new Client('https://*.infobip.com/', 'username', 'password');
 *
 * @package TB\ApiPartners
 * @author  ploginoff
 */
class Client
{
    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * Client constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $username, string $password)
    {
        $this->guzzle = new GuzzleClient([
            'base_uri' => $host,
            'auth' => [$username, $password]
        ]);
    }

    /**
     * Create scenario for WhatsApp only
     *
     * Endpoint: https://{base_url}/omni/1/scenarios
     *
     * @param string $name
     * @param string $from
     * @param string $channel
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createScenario(string $name, string $from, string $channel = 'WHATSAPP') : array
    {
        $params = [
            'name' => $name,
            'flow' => [
                [
                    'from'      => $from,
                    'channel'   => $channel
                ]
            ],
            'default' => true
        ];
        return $this->exec('/omni/1/scenarios', $params);
    }

    /**
     * Send text message
     *
     * Endpoint: https://{base_url}/omni/1/advanced
     *
     * @param string $scenario
     * @param string $phone
     * @param string $message
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendText(string $scenario, string $phone, string $message)
    {
        $params = [
            'scenarioKey' => $scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'text' => $message
            ],
        ];

        return $this->exec('/omni/1/advanced', $params);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function exec(string $path, array $params = [])
    {
        $response = $this->guzzle->request('POST', $path, [
            'json'      => $params,
            'debug'     => true,
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
