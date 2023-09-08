<?php

use PHPlexus\Routing\Router;
use PHPlexus\Http\HttpMethod;
use PHPlexus\Controllers\HomeController;
use PHPlexus\Controllers\MarkdownController;

$router = new Router();

// Simple Routes
$router->add(HttpMethod::GET, '/home', HomeController::class);

// Route Grouping
$router->group('/blog', function() use ($router) {
    $router->add(HttpMethod::GET, '/{category}/{post}', MarkdownController::class);
});

return $router;
