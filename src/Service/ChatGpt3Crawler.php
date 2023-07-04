<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ChatGpt3Crawler implements ChatGpt3CrawlerInterface
{
    private string $apiKey;
    private string $endpoint;
    private Client $client;

    public function __construct(string $apiKey, string $endpoint, Client $client = null)
    {
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
        $this->client = $client ?? new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function ask(
        string $prompt,
        int $maxTokens = 1024,
        int $n = 1,
        string $stop = null,
        float $temperature = 0.7
    ): string {

        $response = $this->client->post($this->endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $this->apiKey),
            ],
            'json' => [
                'prompt' => $prompt,
                'max_tokens' => $maxTokens,
                'n' => $n,
                'stop' => $stop,
                'temperature' => $temperature,
            ],
        ]);

        $decoded_response = json_decode($response->getBody()->getContents(), true);

        return $decoded_response['choices'][0]['text'];
    }
}
