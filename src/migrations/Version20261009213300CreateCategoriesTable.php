<?php

declare(strict_types=1);
namespace app\migrations;

use app\components\Migration;

class Version20261009213300CreateCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec( <<<SQL
            CREATE TABLE `categories` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `description` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL ON UPDATE NOW(),
                `last_article_created_at` datetime DEFAULT NULL 'Дата создания последней добавленной статьи в эту категорию',
                `last_article_ids` json DEFAULT NULL COMMENT 'id последних 10 добавленных статей в эту категорию в порядке убывания created_at',
                PRIMARY KEY (`id`),
                KEY `last_article_created_at` (`last_article_created_at`)
                ) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            SQL
        );
    }

    public function down(): void
    {
        $this->db->exec(<<<SQL
            DROP TABLE IF EXISTS categories
        SQL);
    }
}