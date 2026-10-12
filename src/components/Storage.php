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
            ->prepare(<<< SQL
                SELECT * FROM articles
                WHERE id = ?
                SQL
            );
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $row['categories'] = $this->getArticleCategories($id);
            return Article::fromRow($row);
        }

        return null;
    }

    /**
     * Достает из бд категорию и преобразует в модель Category
     * @param int $id
     * @return ?Category
     */
    public function getCategory(int $id): ?Category
    {
        $statement = $this->pdo()
            ->prepare(<<< SQL
                SELECT c.*, latest.articles_count
                FROM categories c
                LEFT JOIN (
                    SELECT arl.cid, COUNT(*) AS articles_count
                    FROM article_category_link arl
                    GROUP BY arl.cid
                ) latest ON latest.cid = c.id
                WHERE c.id = ?
                SQL
            );
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : Category::fromRow($row);
    }

    /**
     * Достает все категории, отсортированные по последней добавленной статье
     * @return Category[]
     */
    public function getCategories(int $lastArticlesCount = 3): array
    {
        $statement = $this->pdo()
            ->prepare(<<< SQL
                SELECT 
                    c.*,
                    count(arl.aid) as articles_count
                FROM categories AS c
                INNER JOIN article_category_link as arl on arl.cid=c.id
                GROUP BY c.id
                ORDER BY c.last_article_created_at DESC
            SQL);
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $aids = [];

        foreach ($rows as &$row) {
            $row['last_article_ids'] = json_decode($row['last_article_ids'], true);

            $row['last_article_ids'] = array_slice($row['last_article_ids'], 0, $lastArticlesCount);
            $aids = [
                ...$aids,
                ...$row['last_article_ids']
            ];
        }

        $articlesMap = $this->getMapArticles($aids);

        foreach ($rows as &$row) {
            $row['last_articles'] = [];
            foreach ($row['last_article_ids'] as $aid) {
                $row['last_articles'][] = $articlesMap[$aid];
            }

            $row = Category::fromRow($row);
        }

        return $rows;
    }

    private function getMapArticles(array $ids): array
    {
        $idsStr = implode(',', $ids);
        $statement = $this->pdo()
            ->prepare(<<< SQL
                SELECT 
                    a.*
                FROM articles AS a
                WHERE a.id in ({$idsStr})
            SQL);

        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $map = [];

        foreach ($rows as $row) {
            $map[$row['id']] = Article::fromRow($row);
        }

        return $map;
    }

    /**
     * Достает категории, к которым относится статья
     * @param int $id
     * @return Category[]
     */
    public function getArticleCategories(int $id): array
    {
        $statement = $this->pdo()
            ->prepare(<<< SQL
                SELECT c.*
                FROM categories c
                INNER JOIN article_category_link l ON l.cid = c.id
                WHERE l.aid = ?
                ORDER BY c.id
                SQL
            );
        $statement->execute([$id]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => Category::fromRow($row), $rows);
    }

    /**
     * Достает статьи, относящиеся к категории
     * @param int $cid
     * @param string $sort
     * @param int $page
     * @param int $pageSize
     * @return Pager
     */
    public function getArticlesByCategory(int $cid, string $sort = 'created_at', int $page = 1, int $pageSize = 9): Pager
    {
        $order = $sort === 'views' ? 'a.views DESC' : 'a.created_at DESC';
        $sql = <<< SQL
            SELECT a.*
            FROM articles a
            INNER JOIN article_category_link l ON l.aid = a.id
            WHERE l.cid = {$cid}
            ORDER BY {$order}
            SQL;

        /** @var Pager $pager */
        $pager = Container::get(Pager::class);
        $pager->paginate($sql, $page, $pageSize);

        $pager->setItems(array_map(fn(array $row) => Article::fromRow($row), $pager->getItems()));

        return $pager;
    }

    private function pdo(): PDO
    {
        return $this->db->connect();
    }
}