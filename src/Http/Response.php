<?php

namespace PHPlexus\Http;

class Response {
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function setStatusCode(int $statusCode): self {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function addHeader(string $name, string $value): self {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self {
        $this->content = $content;
        return $this;
    }

    public function setHeaders(array $headers): self {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getHeader(string $name): ?string {
        return $this->headers[$name] ?? null;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function send(): void {
        if (!headers_sent()) {
            http_response_code($this->statusCode);

            foreach ($this->headers as $name => $value) {
                header("$name: $value");
            }
        }

        echo $this->content;
    }
}
