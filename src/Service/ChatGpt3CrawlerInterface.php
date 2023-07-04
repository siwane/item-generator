<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Exception\GuzzleException;

interface ChatGpt3CrawlerInterface
{
    /**
     * @throws GuzzleException
     */
    public function ask(
        string $prompt,
        int $maxTokens = 1024,
        int $n = 1,
        string $stop = null,
        float $temperature = 0.7
    ): string;
}
