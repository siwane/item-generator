<?php

declare(strict_types=1);

namespace App\Service;

use JsonSchema\Validator;

class QuizGenerator implements QuizGeneratorInterface
{
    private ChatGpt3Crawler $gpt3;
    private string $jsonSchemaPath;

    public function __construct(ChatGpt3CrawlerInterface $chatGpt3Crawler, string $jsonSchemaPath)
    {
        $this->gpt3 = $chatGpt3Crawler;
        $this->jsonSchemaPath = $jsonSchemaPath;
    }

    public function generate(string $topic, string $difficulty, int $numberOfQuestion = 5): array
    {
        $question_csv = 'As content creator for computer based assessment, ';
        $question_csv .= 'can you create a JSON array with ' . $numberOfQuestion . ' random multiple choice questions with keys "Question","A","B","C","D","Correct Identifier". JSON keys are choice identifiers. The Correct identifier is a choice identifier.';
        $question_csv .= ' The test must be about "' . $topic . '" with a ' . $difficulty . ' difficulty.';

        $time_start = microtime(true);

        $response = $this->gpt3->ask($question_csv);

        $time_end = microtime(true);

        // log time

        while (!$this->validateJsonResponse($response)) {
            $response = $this->gpt3->ask($question_csv);
        }

        return json_decode($response, true);
    }

    private function validateJsonResponse(string $json): bool
    {
        $schema = json_decode(file_get_contents($this->jsonSchemaPath), true);

        $data = json_decode($json, true);

        $validator = new Validator();
        $validator->validate($data, $schema);

        if (!$validator->isValid()) {
            $errors = [];
            foreach ($validator->getErrors() as $error) {
                $errors[] = sprintf("[%s] %s", $error['property'], $error['message']);
            }

            // log error
            echo implode("\n", $errors);
            return false;
        } else {
            return true;
        }
    }
}
