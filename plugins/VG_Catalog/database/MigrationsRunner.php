<?php

namespace App\Database;

use PDO;

class MigrationsRunner
{
    private PDO $pdo;
    private string $migrationsPath;

    public function __construct(PDO $pdo, string $migrationsPath)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = $migrationsPath;

        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $this->pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            filename VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            migrated_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function migrate(): void
    {
        $applied = $this->getAppliedMigrations();
        $files = glob($this->migrationsPath . '/*.php');
        $batch = $this->getNextBatch();

        foreach ($files as $file) {
            $className = $this->getClassNameFromFile($file);

            if (in_array($className, $applied)) {
                continue;
            }

            require_once $file;

            $migration = new $className($this->pdo);
            $migration->up();

            $this->recordMigration($className, $batch, basename($file));

            echo "Migrated: {$className}\n";
        }
    }

    public function rollback(): void
    {
        $batch = $this->getLastBatch();

        if (!$batch) {
            echo "Nothing to rollback.\n";
            return;
        }

        $stmt = $this->pdo->prepare("
            SELECT migration, filename FROM migrations 
            WHERE batch = :batch 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute(['batch' => $batch]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            echo "Nothing to rollback.\n";
            return;
        }

        $className = $row['migration'];
        $filename = $row['filename'];
        $file = $this->migrationsPath . '/' . $filename;

        if (!file_exists($file)) {
            echo "File for {$className} not found.\n";
            return;
        }

        require_once $file;

        $migration = new $className($this->pdo);
        $migration->down();

        $this->pdo->prepare("DELETE FROM migrations WHERE migration = :migration")->execute([
            'migration' => $className
        ]);

        echo "Rolled back: {$className}\n";
    }

    public function create(string $name)
    {
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_{$this->camelToSnakeCase($name)}_table.php";
        $table = strtolower($this->camelToSnakeCase($name));
        $name = ucfirst($name);
        $className = "Create{$name}Table";
        $stub = file_get_contents(__DIR__ . '/stubs/migration.stub');
        $content = str_replace(
            ['{{className}}', '{{tableName}}'],
            [$className, $table],
            $stub
        );

        $path = $this->migrationsPath . '/' . $filename;
        file_put_contents($path, $content);
        echo "Created migration file: {$filename}\n";
    }

    function camelToSnakeCase($string) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    private function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getNextBatch(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
        return (int)$stmt->fetchColumn() + 1;
    }

    private function getLastBatch(): ?int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
        return $stmt->fetchColumn();
    }

    private function recordMigration(string $className, int $batch, string $filename): void
    {
        $this->pdo->prepare("
        INSERT INTO migrations (migration, batch, migrated_at, filename)
        VALUES (:migration, :batch, NOW(), :filename)
    ")->execute([
            'migration' => $className,
            'batch' => $batch,
            'filename' => $filename
        ]);
    }

    private function getClassNameFromFile(string $file): string
    {
        $fileName = pathinfo($file, PATHINFO_FILENAME);

        $parts = explode('_', $fileName);

        $classParts = array_slice($parts, 4);

        $className = implode(' ', $classParts);
        $className = str_replace(' ', '', ucwords($className));

        return 'App\Database\Migrations\\' . $className;
    }

    private function fileNameFromClass(string $className): string
    {
        $shortClassName = (string) substr(strrchr($className, '\\'), 1);

        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortClassName));

        return $snake . '.php';
    }
}