<?php

namespace PHPlexus\Tests\Core\Middleware;

use PHPlexus\Http\Request;
use PHPlexus\Http\Response;
use PHPlexus\Middleware\MiddlewareStack;
use PHPlexus\Middleware\SecurityMiddleware;
use PHPlexus\Interfaces\MiddlewareInterface;
use PHPUnit\Framework\TestCase;

class DummyMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next): Response
    {
        $response->addHeader('X-Dummy', '1');
        return $next($request, $response);
    }
}

class MiddlewareTest extends TestCase
{
    public function testChainsMiddlewaresAndReturnsResponse()
    {
        $stack = new MiddlewareStack();
        $stack->add(new DummyMiddleware());

        $request = new Request();
        $response = new Response();

        $result = $stack->run($request, $response, function ($req, $res) {
            $res->setContent('final');
            return $res;
        });

        $this->assertEquals('1', $result->getHeader('X-Dummy'));
        $this->assertEquals('final', $result->getContent());
    }

    public function testSecurityMiddlewareAddsHeaders()
    {
        $middleware = new SecurityMiddleware();
        $request = new Request();
        $response = new Response();

        $result = $middleware->handle($request, $response, function ($req, $res) {
            return $res;
        });

        $this->assertEquals('DENY', $result->getHeader('X-Frame-Options'));
        $this->assertEquals('nosniff', $result->getHeader('X-Content-Type-Options'));
        $this->assertStringContainsString("default-src 'self'", $result->getHeader('Content-Security-Policy'));
    }

    public function testSecurityMiddlewareBlocksInProduction()
    {
        putenv('APP_ENV=production');
        $middleware = new SecurityMiddleware();
        $request = new Request(); // insecure request
        $response = new Response();

        $result = $middleware->handle($request, $response, function ($req, $res) {
            $this->fail('Should not call next middleware if HTTPS is missing in production');
        });

        $this->assertEquals(403, $result->getStatusCode());
        $this->assertEquals('HTTPS required', $result->getContent());
        putenv('APP_ENV=local'); // reset env
    }
}
