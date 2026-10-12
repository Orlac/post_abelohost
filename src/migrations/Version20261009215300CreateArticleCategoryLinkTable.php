<?php

declare(strict_types=1);
namespace app\migrations;

use app\components\Migration;

class Version20261009215300CreateArticleCategoryLinkTable extends Migration
{
    public function up(): void
    {
        $this->db->exec(<<<SQL
            CREATE TABLE `article_category_link` (
                `aid` int unsigned NOT NULL,
                `cid` int unsigned NOT NULL,
                PRIMARY KEY (`aid`,`cid`) USING BTREE,
                KEY `fk_article_category_link_category` (`cid`),
                CONSTRAINT `fk_article_category_link_article` FOREIGN KEY (`aid`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_article_category_link_category` FOREIGN KEY (`cid`) REFERENCES `categories` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        SQL);
    }

    public function down(): void
    {
        $this->db->exec(<<<SQL
            DROP TABLE IF EXISTS article_category_link
        SQL);
    }
}