<?php

namespace PHPlexus\Controllers;

use PHPlexus\Http\Request;
use PHPlexus\Http\Response;

class HomeController extends BaseController
{
    public function handle(Request $request, Response $response): void
    {
    }
    public function render(Request $request, Response $response): void
    {
        $this->renderView('home', $response, [
            'title' => 'Welcome to PHPlexus!',
            'content' => 'This is the home page. You are visiting ' . $request->getServerParam('REQUEST_URI')
        ]);
    }
}