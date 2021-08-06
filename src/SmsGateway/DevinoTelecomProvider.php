<?php

namespace SmsGateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class DevinoTelecomProvider implements ProviderInterface
{
    protected $login;
    protected $password;
    protected $client;

    const BASE_URL = 'https://integrationapi.net';

    const DEFAULT_SENDER = 'RMSG';

    const TIMEOUT = 30;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
        $this->client = new Client();
    }

    /**
     * Отправляем сообщение
     *
     * @param string $message
     * @param string $recipient
     * @param string $sender (default: null)
     *
     * @return bool
     */
    public function sendMessage($message, $recipient, $sender = null)
    {
        if (!is_string($recipient)) {
            throw new \RuntimeException('Recipient should be a string');
        }

        $sender = empty($sender) ? static::DEFAULT_SENDER : $sender;

        $r = $this->client->post(self::BASE_URL . '/rest/v2/sms/send', [
            'query' => [
                'Login' => $this->login,
                'Password' => $this->password,
                'SourceAddress' => $sender,
                'DestinationAddress' => $recipient,
                'Data' => $message,
            ],
        ]);

        return true;
    }

    public function getApiRequest(array $apiMethod, array $params = [])
    {
        $requestParams = '';
        if (count($params)) {
            $requestParams = [];
            foreach ($params as $key => $value) {
                $requestParams[] = $key . '=' . $value;
            }
            $requestParams = '?' . implode('&', $requestParams);
        }

        $r = $this->client->get($this->url, [
            'query' => $query,
        ]);

        $request = $this->client->createRequest(
            $apiMethod['type'],
            $apiMethod['url'] . $requestParams,
            [
                'timeout' => self::TIMEOUT,
                'connect_timeout' => self::TIMEOUT,
            ]
        );

        return $request;
    }
}
