<?php

namespace RepoScribe\Middleware;

use RepoScribe\Interfaces\MiddlewareInterface;

use RepoScribe\Http\Request;
use RepoScribe\Http\Response;

class MiddlewareStack
{
    private array $middlewares = [];

    public function add(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function run(Request $request, Response $response, callable $final): void
    {
        $middlewareChain = $this->createChain($this->middlewares, $final);
        $middlewareChain($request, $response);
    }

    private function createChain(array $middlewares, callable $final): callable
    {
        $last = $final;

        while ($middleware = array_pop($middlewares)) {
            $last = function (Request $request, Response $response) use ($middleware, $last) {
                $middleware->handle($request, $response, $last);
            };
        }

        return $last;
    }
}
