<?php

declare(strict_types=1);
namespace app\migrations;

use app\components\Migration;

class Version20261009214400CreateArticlesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec(<<<SQL
            CREATE TABLE `articles` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                `description` varchar(511) COLLATE utf8mb4_general_ci NOT NULL,
                `text` text COLLATE utf8mb4_general_ci,
                `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
                `views` int unsigned NOT NULL DEFAULT '0',
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `PK_articles_views` (`views` DESC) USING BTREE,
                KEY `PK_articles_published_at` (`created_at` DESC) USING BTREE
                ) ENGINE=InnoDB AUTO_INCREMENT=501 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            SQL);
    }

    public function down(): void
    {
        $this->db->exec(<<<SQL
            DROP TABLE IF EXISTS articles
        SQL);
    }
}