<?php

namespace app\components;

use PDO;
use Throwable;

class MysqlDatabase implements DatabaseInterface
{
    public function __construct(
        private readonly string $host,
        private readonly string $port,
        private readonly string $database,
        private readonly string $username,
        private readonly string $password
    ) {
    }

    public static function fromEnv(): self
    {
        return new self(
            (string) getenv('DB_HOST'),
            (string) getenv('DB_PORT'),
            (string) getenv('DB_DATABASE'),
            (string) getenv('DB_USERNAME'),
            (string) getenv('DB_PASSWORD')
        );
    }

    public function connect(): PDO
    {
        return new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s', $this->host, $this->port, $this->database),
            $this->username,
            $this->password
        );
    }

    public function check(): string
    {
        try {
            $this->connect();

            return 'ok';
        } catch (Throwable $e) {
            return 'ошибка: ' . $e->getMessage();
        }
    }
}
