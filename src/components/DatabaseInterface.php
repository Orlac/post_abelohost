<?php

declare(strict_types=1);

namespace app\components;

use PDO;

interface DatabaseInterface
{
    public function check(): string;

    public function connect(): PDO;
}
