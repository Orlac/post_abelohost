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
    public function getCategories($id): array
    {
        $statement = $this->pdo()
            ->prepare(
                'SELECT c.*
                 FROM categories c
                 LEFT JOIN (
                     SELECT l.cid AS cid, MAX(COALESCE(a.updated_at, a.created_at)) AS latest
                     FROM article_category_link l
                     JOIN articles a ON a.id = l.aid
                     GROUP BY l.cid
                 ) la ON la.cid = c.id
                 ORDER BY la.latest IS NULL, la.latest DESC, c.id ASC'
            );
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($rows as $row) {
            $categories[] = Category::fromRow($row);
        }

        return $categories;
    }



    private function pdo(): PDO
    {
        return $this->db->connect();
    }
}