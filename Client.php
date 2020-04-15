<?php

namespace TalkBank\Infobip;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

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
    public function __construct(string $host, string $username, string $password, ?string $token = null)
    {
        if ($token) {
            $auth = ['headers' => ['Authorization' => "App $token"]];
        } else {
            $auth = ['auth' => [$username, $password]];
        }
        
        $this->guzzle = new GuzzleClient(['debug' => true, 'base_uri' => $host,] + $auth);
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

        var_dump($params);

        return $this->exec('/omni/1/advanced', $params);
    }

    /**
     * Send template
     *
     * Endpoint: https://{base_url}/omni/1/advanced
     *
     * @param string $phone
     * @param string $template
     * @param string $namespace
     * @param string $lang
     * @param array $placeholders
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendTemplate(string $phone, string $template, string $namespace, string $lang, array $placeholders = [])
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'templateName'      => $template,
                'templateNamespace' => $namespace,
                'templateData'      => $placeholders,
                'language'          => $lang,
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
    public function sendImage(string $phone, string $imageUrl, string $capture)
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'text'      => $capture,
                'imageUrl'  => $imageUrl,
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
                'text'      => $capture,
                'fileUrl'   => $fileUrl,
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
     * @param string $videoUrl
     * @param string $capture
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendVideo(string $phone, string $videoUrl, string $capture)
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'text'      => $capture,
                'videoUrl'  => $videoUrl,
            ],
        ];

        return $this->exec('/omni/1/advanced', $params);
    }

    /**
     * @param string $phone
     * @param float $longitude
     * @param float $latitude
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendLocation(string $phone, float $longitude, float $latitude)
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'longitude' => $longitude,
                'latitude' => $latitude,
                // 'locationName' => 'Name of the location',
                // 'address' =>'Address name',
            ],
        ];

        return $this->exec('/omni/1/advanced', $params);
    }

    /**
     * @param string $phone
     * @param string $name
     * @param string $contact
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendContact(string $phone, string $name, string $contact)
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'contacts' => [
                    'name' => [
                        'firstName' => $name,
                        'formattedName' => $name,
                    ],
                    'phones' => [
                        [
                            'phone' => $contact,
                            'type' => 'MAIN',
                        ]
                    ]
                ],
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
            'json'  => $params,
        //  'debug' => true,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
