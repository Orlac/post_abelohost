<?php

declare(strict_types=1);

namespace app\migrations;

use app\components\Migration;

class Version20261012142400_Triggers extends Migration
{
    public function up(): void
    {
        $this->down();
        $this->db->exec(<<<SQL
            CREATE PROCEDURE refresh_category_last_articles(
                IN p_cid INT UNSIGNED,
                IN p_exclude_aid INT UNSIGNED
                )
                READS SQL DATA
                BEGIN
                DECLARE v_ids  JSON;
                DECLARE v_last DATETIME;

                SELECT
                    CAST(CONCAT('[', IFNULL(GROUP_CONCAT(t.id ORDER BY t.created_at DESC, t.id DESC SEPARATOR ','), ''), ']') AS JSON),
                    MAX(t.created_at)
                INTO v_ids, v_last
                FROM (
                    SELECT a.id, a.created_at
                    FROM article_category_link l
                    JOIN articles a ON a.id = l.aid
                    WHERE l.cid = p_cid
                    AND (p_exclude_aid IS NULL OR a.id <> p_exclude_aid)
                    ORDER BY a.created_at DESC, a.id DESC
                    LIMIT 10
                ) t;

                UPDATE categories
                SET last_article_ids        = v_ids,
                    last_article_created_at = v_last
                WHERE id = p_cid;
                END
        SQL);

        $this->db->exec(<<<SQL
            CREATE PROCEDURE refresh_article_categories(IN p_aid INT UNSIGNED)
                READS SQL DATA
                BEGIN
                DECLARE v_done INT DEFAULT 0;
                DECLARE v_cid  INT UNSIGNED;
                DECLARE cur CURSOR FOR
                    SELECT cid FROM article_category_link WHERE aid = p_aid;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

                OPEN cur;
                read_loop: LOOP
                    FETCH cur INTO v_cid;
                    IF v_done THEN LEAVE read_loop; END IF;
                    CALL refresh_category_last_articles(v_cid, p_aid);  -- статья исключается (она удаляется)
                END LOOP;
                CLOSE cur;
                END
        SQL);

        $this->db->exec(<<<SQL
            CREATE TRIGGER trg_acl_ai AFTER INSERT ON article_category_link
                FOR EACH ROW
                BEGIN
                CALL refresh_category_last_articles(NEW.cid, NULL);
                END
        SQL);

        $this->db->exec(<<<SQL
            CREATE TRIGGER trg_acl_ad AFTER DELETE ON article_category_link
                FOR EACH ROW
                BEGIN
                CALL refresh_category_last_articles(OLD.cid, NULL);
                END
        SQL);

        $this->db->exec(<<<SQL
            CREATE TRIGGER trg_articles_bd BEFORE DELETE ON articles
                FOR EACH ROW
                BEGIN
                CALL refresh_article_categories(OLD.id);
                END
        SQL);
    }

    public function down(): void
    {
        $this->db->exec(<<<SQL
            DROP TRIGGER IF EXISTS trg_acl_ai
        SQL);

        $this->db->exec(<<<SQL
            DROP TRIGGER IF EXISTS trg_acl_ad
        SQL
        );

        $this->db->exec(<<<SQL
            DROP TRIGGER IF EXISTS trg_articles_bd
        SQL
        );

        $this->db->exec(<<<SQL
            DROP PROCEDURE IF EXISTS refresh_article_categories
        SQL
        );

        $this->db->exec(<<<SQL
            DROP PROCEDURE IF EXISTS refresh_category_last_articles
        SQL
        );

        $this->db->exec(<<<SQL
            DROP PROCEDURE IF EXISTS refresh_all_categories
        SQL
        );
    }
}