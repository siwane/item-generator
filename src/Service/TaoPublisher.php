<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use RuntimeException;

class TaoPublisher implements TaoPublisherInterface
{
    private string $user;
    private string $password;
    private string $taoUrl;
    private string $taoClassUri;

    public function __construct(string $user, string $password, string $taoUrl, string $taoClassUri)
    {
        $this->user = $user;
        $this->password = $password;
        $this->taoUrl = $taoUrl;
        $this->taoClassUri = $taoClassUri;
    }

    public function publishPackage(string $filePath): string
    {
        $client = new Client(['verify' => false]);

        $boundary = uniqid();
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->user . ':' . $this->password),
            'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
            'Accept' => 'application/json',
        ];

        $resource = fopen($filePath, 'r');

        $data = [
            [
                'name' => 'content',
                'contents' => $resource,
                'filename' => basename($filePath),
                'headers' => [
                    'Content-Type' => 'application/zip',
                ],
            ],
            [
                'name' => 'class-uri',
                'contents' => $this->taoClassUri,
            ],
        ];

        $request = new Request(
            'POST',
            sprintf('%s/taoQtiItem/RestQtiItem/import/', $this->taoUrl),
            $headers,
            new MultipartStream($data, $boundary)
        );

        $response = $client->send($request);

        fclose($resource);

        if ($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody()->getContents(), true);
            return $body['data']['items'][0];
        }

        throw new RuntimeException('Cannot publish to remote TAO API.');
    }
}
