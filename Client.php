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
     * @param string $token
     */
    public function __construct(string $host, string $token)
    {
        $this->guzzle = new GuzzleClient([
            'base_uri' => $host,
            'headers'  => ['Authorization' => "App $token"],
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
        $whatsApp = [
            'templateName' => $template,
            'templateData' => $placeholders,
            'language'     => $lang,
        ];
        if ($namespace) {
            $whatsApp['templateNamespace'] = $namespace;
        }

        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => $whatsApp,
        ];

        return $this->exec('/omni/1/advanced', $params);
    }

    /**
     * Send media template
     *
     * Endpoint: https://{base_url}/omni/1/advanced
     *
     * @param string $phone
     * @param string $template
     * @param string $lang
     * @param array $header
     * @param array|null $placeholders
     * @param array|null $buttons
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @link https://www.infobip.com/docs/whatsapp/send-whatsapp-over-api#send-interactive-message-templates
     */
    public function sendMediaTemplate(string $phone, string $template, string $lang, ?array $header = null, array $placeholders = [], array $buttons = [])
    {
        $message = [
            'scenarioKey' => $this->scenario,
            'destinations' => [['to' => ['phoneNumber' => $phone]]],
            'whatsApp' => [
                'templateName' => $template,
                'mediaTemplateData' => [
                    'body' => [
                        'placeholders' => $placeholders,
                    ],
                ],
                'language' => $lang,
            ],
        ];

        if (null !== $header) {
            $message['whatsApp']['mediaTemplateData']['header'] = $header;
        }

        if ($buttons) {
            $message['whatsApp']['mediaTemplateData']['buttons'] = $buttons;
        }

        return $this->exec('/omni/1/advanced', $message);
    }

    /**
     * Send image
     *
     * Endpoint: https://{base_url}/omni/1/advanced
     *
     * @param string $phone
     * @param string $imageUrl
     * @param null|string $capture
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendImage(string $phone, string $imageUrl, ?string $capture)
    {
        $params = [
            'scenarioKey' => $this->scenario,
            'destinations' => [[
                'to' => ['phoneNumber' => $phone]
            ]],
            'whatsApp' => [
                'imageUrl'  => $imageUrl,
            ],
        ];

        if (!empty($capture)) {
            $params['whatsApp']['text'] = $capture;
        }

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
