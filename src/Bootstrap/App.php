<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Controller\HomeController;
use DI\Container;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Throwable;

class App
{
    private string $root;

    public function __construct(?string $root = null)
    {
        $this->root = $root ?? __DIR__ . '/../../';
    }

    public function handle(Request $request): Response
    {
        try {
            $container = $this->loadDotEnv();
            $container = $this->loadContainer();
            $matcher = $this->loadRoute($container);

            $parameters = $matcher->match($request->getPathInfo());

            $controller = $parameters['_controller'];

            // Call the controller method with the request as parameter
            $response = $controller($request);

            if (!$response instanceof Response) {
                $response = new Response();
            }
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Route not found.', 404);
        } catch (Throwable $e) {
            $message = sprintf('An error occurred: %s', $e->getMessage());

            if (!isset($container) || !$container->has(HomeController::class)) {
                echo $message;
            } else {
                $controller = $container->get(HomeController::class)
                    ->setMessage($message);
                $response = $controller($request);
            }
        }

        return $response;
    }

    private function loadDotEnv(): void
    {
        $dotenv = Dotenv::createImmutable($this->root);
        $dotenv->safeLoad();
    }

    private function loadContainer(): Container
    {
        # Dependencies injection
        return (new ContainerBuilder())
            ->addDefinitions($this->root . '/config/services.php')
            ->build();
    }

    private function loadRoute(Container $container): UrlMatcher
    {
        $routeDefinitions = require $this->root . '/config/routes.php';

        $routes = new RouteCollection();

        $createRoute = function ($configuration, $name) use ($routes, $container) {
            $routes->add($name, new Route($configuration['path'], array(
                '_controller' => $container->get($configuration['controller']),
            )));
        };

        array_walk($routeDefinitions, $createRoute);

        // Create a UrlMatcher object and use it to match the current request to a route
        return new UrlMatcher($routes, new RequestContext());
    }
}
