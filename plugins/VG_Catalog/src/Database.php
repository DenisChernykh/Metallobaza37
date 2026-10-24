<?php

namespace VG_Catalog;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public static function init()
    {
        if (!defined('DB_NAME')) {
            require_once dirname(__DIR__, 4) . '/wp-config.php';
        }

        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'wp_',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
