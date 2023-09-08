<?php

namespace RepoScribe\Controllers;

use RepoScribe\Http\Request;
use RepoScribe\Http\Response;

class HomeController extends BaseController
{
    public function handle(Request $request, Response $response): void
    {
    }
    public function render(Request $request, Response $response): void
    {
        $this->renderView('home', $response, [
            'title' => 'Welcome to RepoScribe!',
            'content' => 'This is the home page. You are visiting ' . $request->getServerParam('REQUEST_URI')
        ]);
    }
}