<?php

namespace app\components;

use PDO;
use RuntimeException;
use Throwable;

class Migrator
{
    private PDO $db;

    private string $migrationDir;

    public function __construct(PDO $db, string $migrationDir)
    {
        $this->db = $db;
        $this->migrationDir = $migrationDir;

        $this->ensureTable();
    }

    /**
     * @return list<string>
     */
    public function getAvailable(): array
    {
        $files = glob($this->migrationDir . '/*.php') ?: [];
        $names = [];

        foreach ($files as $file) {
            $names[] = pathinfo($file, PATHINFO_FILENAME);
        }

        sort($names);

        return $names;
    }

    /**
     * @return list<string>
     */
    public function getApplied(): array
    {
        $statement = $this->db->query('SELECT id FROM migrations ORDER BY id');

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @return list<string>
     */
    public function getPending(): array
    {
        return array_values(array_diff($this->getAvailable(), $this->getApplied()));
    }

    /**
     * @return list<array{name: string, applied: bool}>
     */
    public function status(): array
    {
        $applied = $this->getApplied();
        $result = [];

        foreach ($this->getAvailable() as $name) {
            $result[] = [
                'name' => $name,
                'applied' => in_array($name, $applied, true),
            ];
        }

        return $result;
    }

    /**
     * @return list<string>
     */
    public function migrate(): array
    {
        $applied = [];

        foreach ($this->getPending() as $name) {
            $this->apply($name);
            $applied[] = $name;
        }

        return $applied;
    }

    /**
     * @return list<string>
     */
    public function rollback(int $steps = 1): array
    {
        $applied = $this->getApplied();
        $steps = max(1, $steps);
        $rolledBack = [];

        for ($i = 0; $i < $steps && $applied !== []; $i++) {
            $name = array_pop($applied);
            $this->revert($name);
            $rolledBack[] = $name;
        }

        return $rolledBack;
    }

    private function apply(string $name): void
    {
        $migration = $this->create($name);

        $this->db->beginTransaction();

        try {
            $migration->up();
            $this->db->prepare('INSERT INTO migrations (id) VALUES (?)')->execute([$name]);
            if ($this->db->inTransaction()) {
                $this->db->commit();
            }
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new RuntimeException(sprintf('Migration %s failed: %s', $name, $e->getMessage()), 0, $e);
        }
    }

    private function revert(string $name): void
    {
        $migration = $this->create($name);

        $this->db->beginTransaction();

        try {
            $migration->down();
            $this->db->prepare('DELETE FROM migrations WHERE id = ?')->execute([$name]);
            if ($this->db->inTransaction()) {
                $this->db->commit();
            }
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new RuntimeException(sprintf('Rollback of %s failed: %s', $name, $e->getMessage()), 0, $e);
        }
    }

    private function create(string $name): Migration
    {
        $class = 'app\\migrations\\' . $name;

        if (!class_exists($class)) {
            throw new RuntimeException("Migration class not found: {$class}");
        }

        $migration = new $class($this->db);

        if (!$migration instanceof Migration) {
            throw new RuntimeException("Migration {$name} must extend app\\components\\Migration");
        }

        return $migration;
    }

    private function ensureTable(): void
    {
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS migrations (
                id VARCHAR(255) PRIMARY KEY,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )'
        );
    }
}
