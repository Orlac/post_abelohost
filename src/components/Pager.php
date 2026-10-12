<?php

declare(strict_types=1);

namespace app\components;

use PDO;
use RuntimeException;

class Pager
{
    private int $page = 1;
    private int $perPage = 20;
    private int $total = 0;
    private int $totalPages = 1;
    private array $items = [];

    public function __construct(private DatabaseInterface $db)
    {
    }

    /**
     * Выполняет разбивку по страницам для заданного SQL-запроса.
     *
     * @return list<array<string, mixed>>
     */
    public function paginate(string $sql, int $page = 1, int $perPage = 20): self
    {
        $page = max(1, $page);
        $this->page = $page;
        $this->perPage = max(1, $perPage);

        $pdo = $this->db->connect();
        $total = (int) $pdo->query($this->countSql($sql))->fetchColumn();
        $this->total = $total;
        $this->totalPages = $total === 0 ? 1 : (int) ceil($total / $perPage);

        if ($page > $this->totalPages) {
            $this->page = $this->totalPages;
        }

        $offset = ($this->page - 1) * $perPage;
        $items = $pdo->query($sql . " LIMIT {$perPage} OFFSET {$offset}")
            ->fetchAll(PDO::FETCH_ASSOC);

        if (!is_array($items)) {
            throw new RuntimeException('Pager failed to fetch items.');
        }

        $this->items = $items;

        return $this;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    public function setItems(array $items): static
    {
        $this->items = $items;
        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->page;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->totalPages;
    }

    public function hasPrev(): bool
    {
        return $this->page > 1;
    }

    /**
     * Собирает COUNT(*) по исходному SQL, убирая ORDER BY / LIMIT.
     */
    private function countSql(string $sql): string
    {
        $sql = preg_replace('/\s+ORDER\s+BY\s+.*?(?=\s+LIMIT\s+|$)/i', '', $sql);
        $sql = preg_replace('/\s+LIMIT\s+.*/i', '', $sql);

        return "SELECT COUNT(*) AS total FROM ({$sql}) AS pager_count";
    }
}