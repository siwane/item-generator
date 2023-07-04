<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeController
{
    private Environment $twig;
    private string $message;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): Response
    {
        return new Response(
            $this->twig->render(
                'home/home.html.twig',
                [
                    'message' => $this->message ?? null,
                ]
            )
        );
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
