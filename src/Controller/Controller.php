<?php

namespace PHPlexus\Controller;

use PHPlexus\Http\Request;
use PHPlexus\Http\Response;
use PHPlexus\Service\Service;

class Controller {
    private Service $service;

    public function __construct(Service $service) {
        $this->service = $service;
    }

    public function handle(Request $request, Response $response): Response {
        $response->setStatusCode(200);
        $response->setContent($this->service->serveData());
        return $response;
    }
}