<?php

namespace app\components;

class Response
{
    private int $statusCode = 200;

    /** @var array<string, string> */
    private array $headers = [];

    private string $content = '';

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }

        echo $this->content;
    }

    public static function text(string $content, int $statusCode = 200): self
    {
        return (new self())
            ->setStatusCode($statusCode)
            ->setHeader('Content-Type', 'text/plain; charset=utf-8')
            ->setContent($content);
    }

    public static function html(string $content, int $statusCode = 200): self
    {
        return (new self())
            ->setStatusCode($statusCode)
            ->setHeader('Content-Type', 'text/html; charset=utf-8')
            ->setContent($content);
    }

    public static function json(mixed $data, int $statusCode = 200): self
    {
        return (new self())
            ->setStatusCode($statusCode)
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setContent((string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        return (new self())
            ->setStatusCode($statusCode)
            ->setHeader('Location', $url)
            ->setContent('');
    }
}
