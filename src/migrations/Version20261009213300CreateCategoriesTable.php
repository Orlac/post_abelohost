<?php

namespace app\migrations;

use app\components\Migration;

class Version20261009213300CreateCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec(
            'CREATE TABLE categories (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                description VARCHAR(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT "",
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci'
        );
    }

    public function down(): void
    {
        $this->db->exec('DROP TABLE IF EXISTS categories');
    }
}