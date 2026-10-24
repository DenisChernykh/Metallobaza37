#!/usr/bin/env php
<?php

require_once __DIR__ . "/database/MigrationsRunner.php";

use App\Database\MigrationsRunner;

$pdo = new PDO('mysql:host=127.0.0.1;dbname=wordpress', 'wordpress', 'wordpress', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$migrationsPath = __DIR__ . '/database/migrations';

$runner = new MigrationsRunner($pdo, $migrationsPath);

$command = $argv[1] ?? null;

switch ($command) {
    case 'migrate':
        $runner->migrate();
        break;

    case 'rollback':
        $runner->rollback();
        break;

    case 'make':
        if (!isset($argv[2])) {
            echo "Use 'make' with table name. 'php migrate.php make table'\n";
            break;
        }
        $runner->create($argv[2]);
        break;

    default:
        echo "Usage: php migrate.php [migrate|rollback|create]\n";
        break;
}