<?php

namespace SmsGateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\XmlParseException;

class WebsmsProvider implements ProviderInterface
{
    const SENDING_DEFAULT_URL = 'http://www.websms.ru/http_in6.asp';

    protected $url;
    protected $user;
    protected $pass;

    protected $client;

    public function __construct($user, $pass, $url = null)
    {
        $this->user = $user;
        $this->pass = $pass;

        $this->url = $url ?: self::SENDING_DEFAULT_URL;

        $this->client = new Client();
    }

    public function sendMessage($message, $recipient, $sender = null)
    {
        $query = [
            'phone_list' => is_array($recipient) ? implode(',', $recipient) : $recipient,
            'message' => $message,
            'format' => 'xml',
            'http_username' => $this->user,
            'http_password' => $this->pass,
        ];

        if ($sender) {
            $query['fromPhone'] = $sender;
        }

        $r = $this->client->get($this->url, [
            'query' => $query,
        ]);

        if (200 !== $r->getStatusCode()) {
            return false;
        }

        try {
            $r = $r->xml();

            return 0 == $r->httpIn[0]->attributes()['error_num'];
        } catch (XmlParseException $e) {
            return false;
        }
    }
}
