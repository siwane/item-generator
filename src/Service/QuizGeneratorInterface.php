<?php

declare(strict_types=1);

namespace App\Service;

interface QuizGeneratorInterface
{
    public function generate(string $topic, string $difficulty, int $numberOfQuestion = 5): array;
}
