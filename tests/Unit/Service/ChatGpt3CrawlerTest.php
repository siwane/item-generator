<?php

declare(strict_types=1);

namespace App\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use App\Service\ChatGpt3Crawler;

class ChatGpt3CrawlerTest extends TestCase
{
    private string $apiKey = 'your-api-key';
    private string $endpoint = 'https://api.example.com/chat';
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->subject = new ChatGpt3Crawler($this->apiKey, $this->endpoint, $this->client);
    }

    public function testAskReturnsExpectedResponse()
    {
        // Prepare mock response
        $mockedResponseBody = json_encode([
            'choices' => [
                [
                    'text' => 'Response from GPT-3'
                ]
            ]
        ]);
        $mockedResponse = new Response(200, [], $mockedResponseBody);

        // Create a mock Guzzle client
        $this->client->expects($this->once())
            ->method('post')
            ->willReturn($mockedResponse);

        // Call the ask method
        $response = $this->subject->ask('Prompt');

        // Assert the response matches the expected value
        $this->assertEquals('Response from GPT-3', $response);
    }
}
