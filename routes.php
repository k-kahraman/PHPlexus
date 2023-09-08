<?php

use RepoScribe\Routing\Router;
use RepoScribe\Http\HttpMethod;
use RepoScribe\Controllers\HomeController;
use RepoScribe\Controllers\MarkdownController;

$router = new Router();

// Simple Routes
$router->add(HttpMethod::GET, '/home', HomeController::class);

// Route Grouping
$router->group('/blog', function() use ($router) {
    $router->add(HttpMethod::GET, '/{category}/{post}', MarkdownController::class);
});

return $router;
