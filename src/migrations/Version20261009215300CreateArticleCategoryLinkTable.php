<?php

namespace app\migrations;

use app\components\Migration;

class Version20261009215300CreateArticleCategoryLinkTable extends Migration
{
    public function up(): void
    {
        $this->db->exec(
            'CREATE TABLE article_category_link (
                aid INT UNSIGNED NOT NULL,
                cid INT UNSIGNED NOT NULL,
                PRIMARY KEY (aid, cid) USING BTREE,
                CONSTRAINT fk_article_category_link_article
                    FOREIGN KEY (aid) REFERENCES articles (id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_article_category_link_category
                    FOREIGN KEY (cid) REFERENCES categories (id)
                    ON DELETE CASCADE
            ) ENGINE = InnoDB'
        );
    }

    public function down(): void
    {
        $this->db->exec('DROP TABLE IF EXISTS article_category_link');
    }
}