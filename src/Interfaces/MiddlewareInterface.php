<?php

namespace RepoScribe\Interfaces;

use RepoScribe\Http\Request;
use RepoScribe\Http\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next): void;
}
