<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\PublishController;
use App\Controller\QuizController;
use App\Controller\QuizValidatorController;

return [
    'home' => [
        'path' => '/',
        'controller' => HomeController::class,
    ],
    'generate' => [
        'path' => '/quiz',
        'controller' => QuizController::class,
    ],
    'submit' => [
        'path' => '/validate',
        'controller' => QuizValidatorController::class,
    ],
    'publish' => [
        'path' => '/publish',
        'controller' => PublishController::class,
    ],
];
