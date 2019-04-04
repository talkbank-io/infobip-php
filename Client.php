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
     * @var string
     */
    protected $scenario;

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
    public function createScenario(string $name, string $from, string $channel = 'WHATSAPP'): array
    {
        $params = [
            'name' => $name,
            'flow' => [
                [
                    'from' => $from,
                    'channel' => $channel
                ]
            ],
            'default' => true
        ];
        return $this->exec('/omni/1/scenarios', $params);
    }

    /**
     * @param string $scenario
     * @return Client
     */
    public function setScenario(string $scenario): self
    {
        $this->scenario = $scenario;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getScenario(): ?string
    {
        return $this->scenario;
    }

    /**
     * Send text message
     *
     * Endpoint: https://{base_url}/omni/1/advanced
     *
     * @param string $phone
     * @param string $message
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendText(string $phone, string $message)
    {
        $params = [
            'scenarioKey' => $this->scenario,
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
     * Send image
     *
     * Endpoint: https://{base_url}/omni/1/advanced
     *
     * @param string $phone
     * @param string $imageUrl
     * @param string $capture
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendImage(string $phone, string $imageUrl, string $capture = null)
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'text' => $capture,
                'imageUrl' => $imageUrl,
            ],
        ];

        return $this->exec('/omni/1/advanced', $params);
    }

    /**
     * Send audio
     *
     * Endpoint: https://{base_url}/omni/1/advanced
     *
     * @param string $phone
     * @param string $audioUrl
     * @param string $capture
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendAudio(string $phone, string $audioUrl)
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'audioUrl' => $audioUrl,
            ],
        ];

        return $this->exec('/omni/1/advanced', $params);
    }

    /**
     * Send file
     *
     * Endpoint: https://{base_url}/omni/1/advanced
     *
     * @param string $phone
     * @param string $fileUrl
     * @param string $capture
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendFile(string $phone, string $fileUrl, string $capture = null)
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'text' => $capture,
                'fileUrl' => $fileUrl,
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
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
