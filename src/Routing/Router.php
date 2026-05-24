<?php

namespace PHPlexus\Routing;

use PHPlexus\Http\Request;

class Router {
    private array $routes = [];
    private array $groupPrefixes = [];

    public function add(string $method, string $pattern, mixed $controllerAction): void {
        $prefix = implode('', $this->groupPrefixes);
        $fullPattern = $prefix . $pattern;
        $this->routes[] = $this->compileRoute($method, $fullPattern, $controllerAction);
    }

    public function group(string $prefix, callable $callback): void {
        $this->groupPrefixes[] = $prefix;
        $callback($this);
        array_pop($this->groupPrefixes);
    }

    private function compileRoute(string $method, string $pattern, mixed $controllerAction): array {
        $pattern = preg_replace_callback('/{([a-zA-Z0-9_]+)(?::([^{}]+))?}/', function (array $matches): string {
            $name = $matches[1];
            $regex = $matches[2] ?? '[^/]+';
            return "(?P<{$name}>{$regex})";
        }, $pattern);

        if (is_string($controllerAction)) {
            $controller = $controllerAction;
            $action = method_exists($controllerAction, 'render') ? 'render' : 'handle';
        } elseif (is_array($controllerAction)) {
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

    public function match(Request $request): ?array {
        $requestedPath = $request->getPath();

        foreach ($this->routes as $route) {
            $matches = [];
            if ($route['method'] === $request->getMethod() && preg_match($route['pattern'], $requestedPath, $matches)) {
                array_shift($matches);
                $request->setRouteParams($matches);
                return ['controller' => $route['controller'], 'action' => $route['action']];
            }
        }
        return null;
    }
}