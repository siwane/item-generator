<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\QuizGenerator;
use App\Service\QuizGeneratorInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class QuizController
{
    private QuizGenerator $quizGenerator;
    private Environment $twig;

    public function __construct(QuizGeneratorInterface $quizGenerator, Environment $twig)
    {
        $this->quizGenerator = $quizGenerator;
        $this->twig = $twig;
    }

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): Response
    {
        $topic = $request->get('topic');
        $difficulty = $request->get('difficulty');

        $quiz = $this->quizGenerator->generate($topic, $difficulty);

        return new Response($this->twig->render(
            'home/quiz.html.twig',
            [
                'quizData' => $quiz,
                'topic' => $topic,
                'difficulty' => $difficulty,
            ]
        ));
    }
}
