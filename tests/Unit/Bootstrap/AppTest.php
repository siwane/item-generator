<?php

declare(strict_types=1);

namespace App\Tests\Bootstrap;

use App\Bootstrap\App;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppTest extends TestCase
{
    public function setUp(): void
    {
        $root = __DIR__ . '/../../Resources';
        $this->app = new App($root);
    }

    public function testHandle(): void
    {
        $request = Request::create('/test', 'GET');
        $response = $this->app->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testHandleControllerException(): void
    {
        $request = Request::create('/error', 'GET');
        $response = $this->app->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Response from HomeController', $response->getContent());
    }

    public function testHandleNotFoundController(): void
    {
        $request = Request::create('/notfound-controller', 'GET');
        $response = $this->app->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('Route not found', $response->getContent());
    }
}
