<?php

namespace PHPlexus\Middleware;

use PHPlexus\Interfaces\MiddlewareInterface;
use PHPlexus\Http\Request;
use PHPlexus\Http\Response;

class MiddlewareStack {
    private array $middlewares = [];

    public function add(MiddlewareInterface $middleware): void {
        $this->middlewares[] = $middleware;
    }

    public function run(Request $request, Response $response, callable $final): Response {
        $middlewareChain = $this->createChain($this->middlewares, $final);
        return $middlewareChain($request, $response);
    }

    private function createChain(array $middlewares, callable $final): callable {
        $last = function (Request $request, Response $response) use ($final): Response {
            $result = $final($request, $response);
            return $result instanceof Response ? $result : $response;
        };

        while ($middleware = array_pop($middlewares)) {
            $last = function (Request $request, Response $response) use ($middleware, $last): Response {
                return $middleware->handle($request, $response, $last);
            };
        }

        return $last;
    }
}
