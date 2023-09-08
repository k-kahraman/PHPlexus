<?php

namespace PHPlexus\Controllers;

use PHPlexus\Http\Request;
use PHPlexus\Http\Response;
use PHPlexus\Templating\TemplateEngine;

abstract class BaseController
{
    protected TemplateEngine $templating;

    public function __construct()
    {
        $viewFile = __DIR__;
        $this->templating = new TemplateEngine('/var/www/html/views');
    }

    abstract public function handle(Request $request, Response $response): void;

    protected function renderView(Response $response, array $data = []): void
    {
        $output = $this->templating->render($data);
        $response->setContent($output);
        $response->send();
    }
}