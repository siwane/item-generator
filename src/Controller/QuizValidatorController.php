<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class QuizValidatorController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws Exception
     */
    public function __invoke(Request $request)
    {
        $topic = $request->get('topic');
        $difficulty = $request->get('difficulty');

        if (!$request->request->has('quizData')) {
            throw new Exception('Request is not submit.');
        }

        $quiz = json_decode($request->request->get('quizData') , true);

        $responses= $request->request->all();
        unset($responses['quizData']);

        return new Response($this->twig->render(
            'home/quiz.html.twig',
            [
                'quizData' => $quiz,
                'topic' => $topic,
                'difficulty' => $difficulty,
                'submittedData' => $responses,
            ]
        ));
    }
}
