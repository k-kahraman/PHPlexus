<?php

namespace PHPlexus\Http;

class Request {
    private array $headers = [];
    private array $queryParams = [];
    private array $postParams = [];
    private array $serverParams = [];
    private array $routeParams = [];
    private string $method;
    private string $uri;
    private string $path;

    private static array $trustedProxies = [];

    public function __construct(?array $server = null, ?array $query = null, ?array $post = null, ?array $headers = null, string $bodySource = 'php://input') {
        $this->serverParams = $server ?? $_SERVER;
        $this->queryParams = $query ?? $_GET;
        $this->postParams = $post ?? $_POST;
        
        if ($headers !== null) {
            $this->headers = $headers;
        } else {
            $this->headers = function_exists('getallheaders') ? getallheaders() : $this->getHeadersFromServerParams($this->serverParams);
        }

        $this->method = $this->serverParams['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->serverParams['REQUEST_URI'] ?? '/';
        $this->path = strtok($this->uri, '?');

        // Handle JSON payloads
        $contentType = $this->getHeader('Content-Type') ?? '';
        if (str_starts_with(strtolower($contentType), 'application/json')) {
            $rawBody = file_get_contents($bodySource);
            if ($rawBody) {
                $decoded = json_decode($rawBody, true);
                if (is_array($decoded)) {
                    $this->postParams = array_merge($this->postParams, $decoded);
                }
            }
        }
    }

    public static function setTrustedProxies(array $proxies): void {
        self::$trustedProxies = $proxies;
    }

    public function getHeader(string $name): ?string {
        $normalized = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $normalized) {
                return $value;
            }
        }
        return null;
    }

    public function getQueryParam(string $name): mixed {
        return $this->queryParams[$name] ?? null;
    }

    public function getPostParam(string $name): mixed {
        return $this->postParams[$name] ?? null;
    }

    public function getQueryParams(): array {
        return $this->queryParams;
    }

    public function getPostParams(): array {
        return $this->postParams;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function isSecure(): bool {
        $https = $this->serverParams['HTTPS'] ?? '';
        if (!empty($https) && strtolower($https) !== 'off') {
            return true;
        }

        if ($this->isFromTrustedProxy()) {
            $proto = $this->getHeader('X-Forwarded-Proto');
            if ($proto !== null) {
                $parts = array_map('trim', explode(',', $proto));
                return strtolower($parts[0]) === 'https';
            }
        }

        return false;
    }

    public function getClientIp(): string {
        $remoteAddr = $this->serverParams['REMOTE_ADDR'] ?? '';
        if ($this->isFromTrustedProxy()) {
            $forwardedFor = $this->getHeader('X-Forwarded-For');
            if ($forwardedFor !== null) {
                $ips = array_map('trim', explode(',', $forwardedFor));
                return end($ips) ?: $remoteAddr;
            }
        }
        return $remoteAddr;
    }

    private function isFromTrustedProxy(): bool {
        $remoteAddr = $this->serverParams['REMOTE_ADDR'] ?? '';
        return in_array($remoteAddr, self::$trustedProxies, true);
    }

    public function getServerParam(string $name): mixed {
        return $this->serverParams[$name] ?? null;
    }

    public function getRouteParam(string|int $key): mixed {
        return $this->routeParams[$key] ?? null;
    }

    public function getRouteParams(): array {
        return $this->routeParams;
    }

    public function setRouteParams(array $params): void {
        $this->routeParams = $params;
    }

    private function getHeadersFromServerParams(array $server): array {
        $headers = [];
        foreach ($server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$name] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }
}
