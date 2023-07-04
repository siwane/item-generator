<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap\App;
use Symfony\Component\HttpFoundation\Request;

$app = new App();
$response = $app->handle(Request::createFromGlobals());
$response->send();
