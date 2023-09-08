<?php

/**
 * ScribeServe - PHP Server Framework
 *
 * ScribeServe is a robust and efficient PHP server framework, designed to cater 
 * to modern web applications' needs with a focus on scalability, flexibility, 
 * and performance. Being the heart of the RepoScribe project, ScribeServe 
 * provides the backbone for server-side rendering (SSR) and various essential 
 * server-side operations, ensuring optimal SEO and user experience.
 * 
 * RepoScribe's GitHub repository: https://github.com/repo-scribe
 *
 * @package     ScribeServe
 * @author      Kaan Kahraman <kaan@kahraman.io>
 * @license     MIT License (See LICENSE file for details)
 * @see         LICENSE file for more information
 * @link        https://github.com/kaankahraman/repo-scribe
 * @version     0.0.3 Alpha
 */

require_once __DIR__ . '/../src/bootstrap.php'; // Load our bootstrap file
$router = require __DIR__ . '/../routes.php'; // Load our routes

use RepoScribe\Http\Request;
use RepoScribe\Http\Response;
use RepoScribe\Middleware\SecurityMiddleware;
use RepoScribe\Middleware\MiddlewareStack;

$request = new Request();
$response = new Response();

$middlewareStack = new MiddlewareStack();
$middlewareStack->add(new SecurityMiddleware());

$middlewareStack->run($request, $response, function ($request, $response) use ($router) {
    $controllerAction = $router->match($request);
    if ($controllerAction) {
        $controllerClass = $controllerAction['controller'];
        $action = $controllerAction['action'];
        $controller = new $controllerClass();
        $controller->$action($request, $response); // Dynamically call the appropriate method.
    }
});