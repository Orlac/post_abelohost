<?php

class Container
{
    /** @var array<string, Closure> */
    private static array $factories = [];

    /** @var array<string, mixed> */
    private static array $instances = [];

    /** @var array<string, true> */
    private static array $resolving = [];

    public static function set(string $id, callable $factory): void
    {
        unset(self::$instances[$id]);
        self::$factories[$id] = Closure::fromCallable($factory);
    }

    public static function get(string $id, array $properties = []): mixed
    {
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        if (isset(self::$factories[$id])) {
            return self::$instances[$id] = (self::$factories[$id])();
        }

        $instance = self::build($id);

        foreach ($properties as $name => $value) {
            $instance->{$name} = $value;
        }

        return self::$instances[$id] = $instance;
    }

    public static function has(string $id): bool
    {
        return isset(self::$factories[$id]) || isset(self::$instances[$id]) || class_exists($id);
    }

    private static function build(string $id): mixed
    {
        if (isset(self::$resolving[$id])) {
            throw new RuntimeException("Circular dependency detected: {$id}");
        }

        if (!class_exists($id)) {
            throw new RuntimeException("Dependency not found: {$id}");
        }

        $reflector = new ReflectionClass($id);

        if (!$reflector->isInstantiable()) {
            throw new RuntimeException("Class is not instantiable: {$id}");
        }

        self::$resolving[$id] = true;

        try {
            return $reflector->newInstanceArgs(self::resolveArguments($reflector));
        } finally {
            unset(self::$resolving[$id]);
        }
    }

    /**
     * @return list<mixed>
     */
    private static function resolveArguments(ReflectionClass $reflector): array
    {
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $arguments[] = self::resolveParameter($parameter);
        }

        return $arguments;
    }

    private static function resolveParameter(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            return self::get($type->getName());
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new RuntimeException(sprintf(
            'Cannot resolve constructor parameter $%s',
            $parameter->getName()
        ));
    }
}
