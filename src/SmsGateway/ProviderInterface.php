<?php

namespace SmsGateway;

interface ProviderInterface
{
    /**
     * Send the message
     *
     * @param string $message
     * @param mixed  $recipient
     * @param string $sender    (default: null)
     *
     * @return bool
     */
    public function sendMessage($message, $recipient, $sender = null);
}
