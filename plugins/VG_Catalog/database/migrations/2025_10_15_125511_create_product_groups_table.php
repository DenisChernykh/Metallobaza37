<?php

namespace App\Database\Migrations;

require_once __DIR__ . '/../Migration.php';

use App\Database\Migration;

class CreateProductGroupsTable extends Migration
{
    public function up(): void
    {
        $this->pdo->exec("
            CREATE TABLE wp_vg_product_groups (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id BIGINT(20) UNSIGNED NOT NULL,
                group_id BIGINT(20) UNSIGNED NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY unique_product_group (product_id, group_id),
                FOREIGN KEY (group_id) REFERENCES wp_posts (id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES wp_posts (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS wp_vg_product_groups;");
    }
}