<?php

namespace RepoScribe\Controllers;

use RepoScribe\Http\Request;
use RepoScribe\Http\Response;
use RepoScribe\lib\Parsedown;

class MarkdownController extends BaseController
{
    public function handle(Request $request, Response $response): void
    {
        $category = $request->getRouteParam(0);
        $post = $request->getRouteParam(1);

        $filePath = '/var/www/html/storage/' . $category . '/' . $post . '.md';

        if (!file_exists($filePath)) {
            $response->setStatusCode(404);
            $response->setContent("Not Found");
            $response->send();
            return;
        }

        $markdownContent = file_get_contents($filePath);

        // Parsing markdown to HTML using Parsedown
        $parsedown = new Parsedown();
        $htmlContent = $parsedown->text($markdownContent);

        print_r($htmlContent);

        $response->setStatusCode(200);
        $response->setContent($htmlContent);
        $response->send();
    }
}
