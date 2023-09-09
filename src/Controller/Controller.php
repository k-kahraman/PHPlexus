<?php

namespace PHPlexus\Controller;

use PHPlexus\Core\Entity;
use PHPlexus\Http\Request;
use PHPlexus\Http\Response;
use PHPlexus\Service\Service;
class Controller {
    private $service;

    public function __construct(Service $service) {
        $this->service = $service;
    }

    public function handle(Request $request, Response $response) {
        $response->setStatusCode(200);
        $response->setContent($this->service->serveData());
        $response->send();
    }
}