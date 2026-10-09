<?php

namespace app\components;

use PDO;

abstract class Migration
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    abstract public function up(): void;

    abstract public function down(): void;
}
