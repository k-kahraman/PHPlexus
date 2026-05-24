<?php

namespace PHPlexus\Middleware;

use PHPlexus\Interfaces\MiddlewareInterface;
use PHPlexus\Http\Request;
use PHPlexus\Http\Response;

class SecurityMiddleware implements MiddlewareInterface {
    public function handle(Request $request, Response $response, callable $next): Response {
        // Block non-HTTPS requests in production environment using trusted-proxy aware isSecure()
        if (getenv('APP_ENV') === 'production' && !$request->isSecure()) {
            $response->setStatusCode(403);
            $response->setContent("HTTPS required");
            return $response;
        }

        // Modern Security Headers
        $response->addHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");

        if ($request->isSecure()) {
            $response->addHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        $response->addHeader('X-Frame-Options', 'DENY');
        $response->addHeader('X-Content-Type-Options', 'nosniff');
        $response->addHeader('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->addHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $next($request, $response);
    }
}
