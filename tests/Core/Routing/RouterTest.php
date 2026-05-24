<?php

namespace PHPlexus\Tests\Core\Routing;

use PHPlexus\Http\Request;
use PHPlexus\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRouteMatchingWithNamedParams()
    {
        $router = new Router();
        $router->add('GET', '/user/{id:\d+}', 'UserController');

        $server = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/user/42'];
        $request = new Request($server);

        $match = $router->match($request);

        $this->assertNotNull($match);
        $this->assertEquals('UserController', $match['controller']);
        $this->assertEquals('handle', $match['action']);
        $this->assertEquals('42', $request->getRouteParam('id'));
        $this->assertEquals('42', $request->getRouteParam(0));
    }

    public function testRouteMatchingIgnoresQueryString()
    {
        $router = new Router();
        $router->add('GET', '/about', 'AboutController');

        $server = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/about?section=team&lang=en'];
        $request = new Request($server);

        $match = $router->match($request);

        $this->assertNotNull($match);
        $this->assertEquals('AboutController', $match['controller']);
    }

    public function testRouteGroups()
    {
        $router = new Router();
        $router->group('/api', function ($router) {
            $router->group('/v1', function ($router) {
                $router->add('GET', '/users', 'ApiUserController');
            });
        });

        $server = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/api/v1/users'];
        $request = new Request($server);

        $match = $router->match($request);

        $this->assertNotNull($match);
        $this->assertEquals('ApiUserController', $match['controller']);
    }
}
