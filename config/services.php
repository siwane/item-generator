<?php

declare(strict_types=1);

use App\Controller\PublishController;
use App\Service\ChatGpt3Crawler;
use App\Service\QtiItemPackageCreator;
use App\Service\QuizGenerator;
use App\Service\QuizGeneratorInterface;
use App\Service\TaoPublisher;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$root = __DIR__ . '/../';

return [

    // parameters
    'gpt3.key' => $_ENV['API_KEY'],
    'gpt3.url' => $_ENV['API_URL'],

    'tao.user' => $_ENV['TAO_USER'],
    'tao.password' => $_ENV['TAO_PWD'],
    'tao.url' => $_ENV['TAO_URL'],
    'tao.classUri' => $_ENV['TAO_IMPORT_ITEM_CLASS'],

    // Twig
    Environment::class => new Environment(new FilesystemLoader($root . '/templates')),

    //Service
    ChatGpt3Crawler::class => DI\create()->constructor(
        DI\get('gpt3.key'), DI\get('gpt3.url')
    ),

    QuizGeneratorInterface::class => DI\create(QuizGenerator::class)->constructor(
        Di\get(ChatGpt3Crawler::class), $root . '/resources/json-schema.json'
    ),

    TaoPublisher::class => DI\create()->constructor(
        DI\get('tao.user'), DI\get('tao.password'), DI\get('tao.url'), DI\get('tao.classUri')
    ),

    QtiItemPackageCreator::class => Di\Create(QtiItemPackageCreator::class)->constructor($root . '/resources'),

    // Controller
    PublishController::class => DI\create(PublishController::class)->constructor(
        DI\get(QtiItemPackageCreator::class),
        DI\get(TaoPublisher::class),
        DI\get('tao.url')
    ),

];
