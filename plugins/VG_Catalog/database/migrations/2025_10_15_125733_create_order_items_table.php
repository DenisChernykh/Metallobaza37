<?php

namespace App\Database\Migrations;

require_once __DIR__ . '/../Migration.php';

use App\Database\Migration;

class CreateOrderItemsTable extends Migration
{
    public function up(): void
    {
        $this->pdo->exec("
            CREATE TABLE wp_vg_order_items (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                order_id INT UNSIGNED NOT NULL,
                product_id BIGINT(20) UNSIGNED NOT NULL,
                quantity INT(11) DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                FOREIGN KEY (order_id) REFERENCES wp_vg_orders (id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES wp_vg_products (post_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS wp_vg_order_items;");
    }
}