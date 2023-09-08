<?php

namespace PHPlexus\Routing;

use PHPlexus\Http\Request;

class Router
{
    private array $routes = [];
    private array $groups = [];

    // Add a single route
    public function add(string $method, string $pattern, $controllerAction): void
    {
        $this->routes[] = $this->compileRoute($method, $pattern, $controllerAction);
    }


    // Define a route group
    public function group(string $prefix, callable $callback): void
    {
        $this->groups[] = ['prefix' => $prefix, 'callback' => $callback];
    }

    // Compile a single route into the internal format
    private function compileRoute(string $method, string $pattern, $controllerAction): array
    {
        $pattern = preg_replace("/{[^}]+}/", "([^/]+)", $pattern);

        // If controllerAction is a string, then it's a class name. Default to "handle" method.
        if (is_string($controllerAction)) {
            $controller = $controllerAction;
            $action = method_exists($controllerAction, 'render') ? 'render' : 'handle'; // If render exists, it'll be used
        }
        // If it's an array, then the first element is class, and the second is method.
        else if (is_array($controllerAction)) {
            [$controller, $action] = $controllerAction;
        } else {
            throw new \InvalidArgumentException("Invalid controller action format.");
        }

        return [
            'method' => $method,
            'pattern' => '@^' . $pattern . '$@i',
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function match(Request $request): ?array
    {
        $requestedUri = $request->getServerParam('REQUEST_URI');

        foreach ($this->routes as $route) {
            $matches = [];
            if ($route['method'] == $request->getMethod() && preg_match($route['pattern'], $requestedUri, $matches)) {
                array_shift($matches);
                $request->setRouteParams($matches);
                return ['controller' => $route['controller'], 'action' => $route['action']];
            }
        }
        return null;
    }
}