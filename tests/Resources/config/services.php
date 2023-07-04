<?php

declare(strict_types=1);

use App\Controller\HomeController;
use Symfony\Component\HttpFoundation\Response;

return [
    'FixtureController' => new class{
        function __invoke() {}
    },
    'ErrorController' => new class{
        function __invoke() { throw new RuntimeException('Controller exception'); }
    },
    HomeController::class => new class{
        function __invoke() { return new Response('Response from HomeController'); }
        function setMessage() { return $this; }
    },
];