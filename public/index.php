<?php

/**
 * PHPlexus - Minimalistic PHP Backend Framework
 *
 * PHPlexus is a sleek, minimalistic PHP backend framework engineered for performance and simplicity.
 * Catering to modern web applications, it offers a balance of scalability, flexibility, and performance.
 * As the cornerstone of the PHPlexus project, it provides the infrastructure for server-side rendering 
 * (SSR) and a plethora of essential server-side operations, championing both optimal SEO and user experience.
 * 
 * PHPlexus's GitHub repository: https://github.com/kaankahraman/PHPlexus
 *
 * @package     PHPlexus
 * @author      Kaan Kahraman <kaan@kahraman.io>
 * @license     MIT License (See LICENSE file for details)
 * @see         LICENSE file for more information
 * @link        https://github.com/kaankahraman/PHPlexus
 * @version     0.0.3 Alpha
 */


$container = require __DIR__ . '/../src/bootstrap.php'; // Load our bootstrap file

$router = require __DIR__ . '/../routes.php'; // Load our routes

use PHPlexus\Http\Request;
use PHPlexus\Http\Response;
use PHPlexus\Middleware\SecurityMiddleware;
use PHPlexus\Middleware\MiddlewareStack;

$request = new Request();
$response = new Response();

$middlewareStack = new MiddlewareStack();
$middlewareStack->add(new SecurityMiddleware());

$middlewareStack->run($request, $response, function ($request, $response) use ($router, $container) {
    $controllerAction = $router->match($request);
    if ($controllerAction) {
        $controllerClass = $controllerAction['controller'];
        $action = $controllerAction['action'];
        $controller = $container->make($controllerClass);
        $controller->$action($request, $response);
    }
});