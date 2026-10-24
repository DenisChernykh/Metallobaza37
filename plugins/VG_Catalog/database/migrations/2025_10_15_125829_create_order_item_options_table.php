<?php

namespace App\Database\Migrations;

require_once __DIR__ . '/../Migration.php';

use App\Database\Migration;

class CreateOrderItemOptionsTable extends Migration
{
    public function up(): void
    {
        $this->pdo->exec("
            CREATE TABLE wp_vg_order_item_options (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                order_item_id INT UNSIGNED NOT NULL,
                option_id BIGINT(20) UNSIGNED NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                FOREIGN KEY (order_item_id) REFERENCES wp_vg_order_items (id) ON DELETE CASCADE,
                FOREIGN KEY (option_id) REFERENCES wp_vg_options (post_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS wp_vg_order_item_options;");
    }
}