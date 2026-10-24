<?php

namespace App\Database\Migrations;

require_once __DIR__ . '/../Migration.php';

use App\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        $this->pdo->exec("
            CREATE TABLE wp_vg_products (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                description TEXT,
                price FLOAT(2) NOT NULL,
                sale_price FLOAT(2),
                sku VARCHAR(255) NOT NULL,
                post_id BIGINT(20) UNSIGNED NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                FOREIGN KEY (post_id) REFERENCES wp_posts (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS wp_vg_products;");
    }
}