<?php

declare(strict_types=1);

namespace app\components;

use PDO;
use app\models\Article;
use app\models\Category;

class Storage
{
    public function __construct(private readonly DatabaseInterface $db)
    {
    }

    /**
     * Достает из бд статью и преобразует в модель Article
     * @param int $id
     * @return ?Article
     */
    public function getArticle(int $id): ?Article
    {
        $statement = $this->pdo()
            ->prepare('SELECT * FROM articles WHERE id = ?');
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : Article::fromRow($row);
    }

    /**
     * Достает из бд категорию и преобразует в модель Category
     * @param int $id
     * @return ?Category
     */
    public function getCategory(int $id): ?Category
    {
        $statement = $this->pdo()
            ->prepare('SELECT * FROM categories WHERE id = ?');
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : Category::fromRow($row);
    }

    /**
     * Достает все категории, отсортированные по последней добавленной/измененной статье
     * @return Category[]
     */
    public function getCategories(): array
    {
        $statement = $this->pdo()
            ->prepare(
                <<< SQL
                SELECT c.*, latest.last_created
                FROM categories AS c
                inner JOIN (
                    SELECT arl.cid, MAX(a.created_at) AS last_created
                    FROM articles AS a
                    INNER JOIN article_category_link AS arl ON a.id = arl.aid
                    GROUP BY arl.cid
                ) AS latest ON c.id = latest.cid
                ORDER BY latest.last_created DESC
                SQL
            );
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => Category::fromRow($row), $rows);
    }

    /**
     * Последние добавленные $count статьи
     * @param int $count
     * @param ?int $cid // id категории
     * @return Article[]
     */
    public function getLatestPosts(int $count = 3, ?int $cid = null): array
    {
        $sql = 'SELECT * FROM articles';

        $params = [];

        if ($cid !== null) {
            $sql .= ' WHERE id IN (SELECT aid FROM article_category_link WHERE cid = ?)';
            $params[] = $cid;
        }

        $sql .= ' ORDER BY created_at DESC LIMIT ' . $count;

        $statement = $this->pdo()->prepare($sql);
        $statement->execute($params);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => Article::fromRow($row), $rows);
    }

    private function pdo(): PDO
    {
        return $this->db->connect();
    }
}