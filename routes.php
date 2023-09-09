<?php

use PHPlexus\Controller\Controller;
use PHPlexus\Routing\Router;
use PHPlexus\Http\HttpMethod;

$router = new Router();

$router->add(HttpMethod::GET, '/', Controller::class);

return $router;
