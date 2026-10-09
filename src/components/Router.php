<?php

class Router
{
    /** @var list<array{method: string, path: string, handler: callable}> */
    private array $routes = [];

    public function __construct(
        private readonly Closure $notFoundHandler,
        private readonly Closure $methodNotAllowedHandler
    ) {
    }

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = $this->normalizePath($uri);
        $allowed = [];

        foreach ($this->routes as $route) {
            $params = [];
            if (!$this->match($route['path'], $path, $params)) {
                continue;
            }
            if ($route['method'] !== $method) {
                $allowed[] = $route['method'];
                continue;
            }

            echo call_user_func_array($route['handler'], $params);
            return;
        }

        if ($allowed !== []) {
            http_response_code(405);
            header('Allow: ' . implode(', ', array_unique($allowed)));
            echo ($this->methodNotAllowedHandler)($path);
            return;
        }

        http_response_code(404);
        echo ($this->notFoundHandler)($path);
    }

    private function normalizePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rawurldecode($path);
        $path = preg_replace('#/+#', '/', $path) ?? '/';

        if (str_ends_with($path, 'index.php')) {
            $path = rtrim(substr($path, 0, -strlen('index.php')), '/');
        }
        if (str_ends_with($path, '.php')) {
            $path = substr($path, 0, -strlen('.php'));
        }

        $path = rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }

    /**
     * @param array<string, string> $params
     */
    private function match(string $routePath, string $path, array &$params): bool
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#D';

        if (!preg_match($pattern, $path, $matches)) {
            return false;
        }

        $params = array_filter($matches, static fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY);

        return true;
    }
}
