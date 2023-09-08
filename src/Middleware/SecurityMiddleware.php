<?php

namespace RepoScribe\Middleware;

use RepoScribe\Interfaces\MiddlewareInterface;
use RepoScribe\Http\Request;
use RepoScribe\Http\Response;

class SecurityMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next): void
    {
        // Block non-HTTPS requests only in production environment
        if (getenv('APP_ENV') === 'production' && !$request->getServerParam('HTTPS')) {
            $response->setStatusCode(403);
            $response->setContent("HTTPS required");
            $response->send();
            return;
        }

        // 2. XSS Protection
        $response->addHeader('X-XSS-Protection', '1; mode=block');

        // 3. HSTS - Ensuring HTTPS is always used
        $response->addHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // 4. Frame protection - deny embedding page into an iframe
        $response->addHeader('X-Frame-Options', 'DENY');

        // 5. Disable content type sniffing
        $response->addHeader('X-Content-Type-Options', 'nosniff');

        // 6. Referrer policy
        $response->addHeader('Referrer-Policy', 'no-referrer-when-downgrade');

        // Continue to the next middleware or request handler
        $next($request, $response);
    }
}
