<?php

namespace App\Integrations\Bitly;

use App\Core\Logger\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BitlyAPI
{

    private Client $http;

    private LoggerInterface $logger;

    private string $token;

    private string $guid;

    public function __construct(string $token, string $guid, Client $http, LoggerInterface $logger)
    {
        $this->token = $token;
        $this->guid = $guid;
        $this->http = $http;
        $this->logger = $logger;
    }

    public function getShortLink(string $url)
    {
        try {
            $response = $this->http->post('https://api-ssl.bitly.com/v4/shorten', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'long_url' => $url,
                    'domain' => 'bit.ly',
                    'group_guid' => $this->guid,
                ],
            ]);
            return json_decode($response->getBody(), true)['link'] ?? $url;
        } catch (\Exception|GuzzleException $e) {
            $this->logger->error("Bitly:getShortLink", $e->getMessage());
        }
        return $url;
    }
}