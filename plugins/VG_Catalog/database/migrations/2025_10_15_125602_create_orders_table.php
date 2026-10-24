<?php

namespace App\Database\Migrations;

require_once __DIR__ . '/../Migration.php';

use App\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        $this->pdo->exec("
            CREATE TABLE wp_vg_orders (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT(20) UNSIGNED,
                name VARCHAR(255),
                phone VARCHAR(255),
                additional JSON,
                status ENUM('PROGRESS', 'COMPLETED', 'CANCELED', 'WAITING') NOT NULL DEFAULT 'PROGRESS',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                FOREIGN KEY (user_id) REFERENCES wp_users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS wp_vg_orders;");
    }
}