<?php

namespace PHPlexus\Http;

class Request
{
    private array $headers;
    private array $queryParams;
    private array $postParams;
    private array $serverParams;
    private array $routeParams;
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->headers = getallheaders();
        $this->queryParams = $this->sanitizeInputArray(INPUT_GET) ?? [];
        $this->postParams = $this->sanitizeInputArray(INPUT_POST) ?? [];
        $this->serverParams = $_SERVER;
        $this->routeParams = [];
        $this->method = $this->serverParams['REQUEST_METHOD'];
        $this->uri = strtok($this->serverParams['REQUEST_URI'], '?');
    }

    private function sanitizeInputArray(int $type): ?array
    {
        $data = filter_input_array($type);
        if (is_array($data)) {
            return array_map('htmlspecialchars', $data);
        }
        return null;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getQueryParam(string $name): ?string
    {
        return $this->queryParams[$name] ?? null;
    }

    public function getPostParam(string $name): ?string
    {
        return $this->postParams[$name] ?? null;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getServerParam(string $name): ?string
    {
        return $this->serverParams[$name] ?? null;
    }

    public function getRouteParam(int $index): ?string
    {
        return $this->routeParams[$index] ?? null;
    }

    public function setRouteParams(array $params)
    {
        $this->routeParams = $params;
    }
}
