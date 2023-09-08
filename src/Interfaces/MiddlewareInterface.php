<?php

namespace PHPlexus\Interfaces;

use PHPlexus\Http\Request;
use PHPlexus\Http\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next): void;
}
