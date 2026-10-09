<?php

namespace app\migrations;

use app\components\Migration;

class Version20261009214400CreateArticlesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec(
            'CREATE TABLE articles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description VARCHAR(511) NOT NULL,
                text TEXT NULL DEFAULT NULL,
                image VARCHAR(255) NULL DEFAULT NULL,
                views INT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                INDEX `PK_articles_views` (`views` DESC) USING BTREE,
                INDEX `PK_articles_published_at` (`created_at` DESC) USING BTREE
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci'
        );
    }

    public function down(): void
    {
        $this->db->exec('DROP TABLE IF EXISTS articles');
    }
}