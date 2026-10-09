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

            echo $this->call($route['handler'], $params);
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

    /**
     * @param array<string, string> $params
     */
    private function call(callable $handler, array $params): mixed
    {
        $closure = $handler instanceof Closure ? $handler : Closure::fromCallable($handler);
        $reflection = new ReflectionFunction($closure);
        $arguments = [];
        $used = [];

        foreach ($reflection->getParameters() as $parameter) {
            if (array_key_exists($parameter->getName(), $params)) {
                $used[$parameter->getName()] = true;
                $arguments[] = $params[$parameter->getName()];
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            $leftover = array_diff_key($params, $used);
            $name = array_key_first($leftover);
            if ($name !== null) {
                $used[$name] = true;
                $arguments[] = $params[$name];
                continue;
            }

            throw new RuntimeException(sprintf(
                'Route handler %s requires missing argument $%s',
                $this->describe($handler),
                $parameter->getName()
            ));
        }

        return $closure(...$arguments);
    }

    private function describe(callable $handler): string
    {
        if (is_string($handler)) {
            return $handler;
        }

        if (is_array($handler)) {
            return (is_string($handler[0]) ? $handler[0] : $handler[0]::class) . '::' . $handler[1];
        }

        return 'closure';
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
        $routePath = $this->normalizeRoutePath($routePath);

        $required = [];
        preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^{}]*))?\}#', $routePath, $required, PREG_SET_ORDER);

        $pattern = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^{}]*))?\}#',
            static function (array $matches): string {
                $regex = ($matches[2] ?? '') !== '' ? $matches[2] : '[^/]+';

                return '(?<' . $matches[1] . '>' . $regex . ')';
            },
            $routePath
        );

        $pattern = '#^' . $pattern . '$#D';

        if (!preg_match($pattern, $path, $matches)) {
            return false;
        }

        $params = array_filter($matches, static fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY);

        foreach ($required as $item) {
            if (($params[$item[1]] ?? '') === '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeRoutePath(string $path): string
    {
        $path = preg_replace('#/+#', '/', $path) ?? '/';
        $path = rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }
}
