<?php

namespace VG_Mailer\src\services\db;

use Exception;
use PDO;
use PDOException;
use VG_Mailer\src\Core;
use VG_Mailer\src\logs\enums\JsLogEnum;
use VG_Mailer\src\logs\JsLog;
use VG_Mailer\src\models\DBModel;
use VG_Mailer\src\Response;

abstract class DBWorker
{
    protected static ?PDO $pdo;
    /**
     * @var DBWorker[]
     */
    private static ?array $models;
    private static bool $canUse = false;
    public string $tableName;
    protected string $dbPrefix = 'vg_mailer_';
    protected array $allowedFields = [];

    public function __construct()
    {
        if (self::$canUse) {
            $this->tableName = $this->convertClassNameToTableName(
                get_called_class()
            );
            $this->initTable();
        }
    }

    private function convertClassNameToTableName(string $className): string
    {
        $shortName = substr(strrchr($className, '\\'), 1);
        $shortName = $this->dbPrefix.$shortName;

        // Удаляем 'Worker' на конце строки
        $shortName = preg_replace('/Worker$/', '', $shortName);

        // Заменяем заглавные буквы на строчные с добавлением '_'
        $output = strtolower(
            preg_replace('/(?<!^)(?=[A-Z])/', '_', $shortName)
        );

        return $output;
    }

    abstract protected function initTable(): void;

    public static function isCanUse(): bool
    {
        return self::$canUse;
    }

    public static function initConnection(): void
    {
        $core = Core::make();
        $dbModel = DBModel::make();

        if (!$dbModel->isUse()) {
            if ($core->isDebug()) {
                JsLog::addLog(JsLogEnum::INFO, 'Сохранение заявок отключено');
            }
            return;
        }

        $dsn = "mysql:host={$dbModel->getHost()};port={$dbModel->getPort()};dbname={$dbModel->getDbName()}";
        try {
            self::$pdo = new PDO(
                $dsn,
                $dbModel->getUsername(),
                $dbModel->getPassword(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            self::$canUse = true;
        } catch (PDOException $e) {
            $message = 'Ошибка подключения к базе данных: '.$e->getMessage();

            if ($core->isDebug()) {
                JsLog::addLog(JsLogEnum::ERROR, $message);
            }
            return;
        }

        JsLog::addLog(JsLogEnum::INFO, 'Сохранение заявок включено');
    }

    public static function make(string $className)
    {
        if (!class_exists($className)) {
            throw new Exception('class '.$className.' does not exist');
        }

        if (!isset(self::$models[$className])) {
            $class = new $className();
            if (!$class instanceof DBWorker) {
                throw new Exception('class '.$className.' does not implement DBModel');
            }
            self::$models[$className] = $class;
        }
        return self::$models[$className];
    }

    public function findAll(?array $data = []): Response
    {
        $queryString = "SELECT * FROM {$this->tableName} WHERE 1=1";
        $countQueryString = "SELECT COUNT(*) FROM {$this->tableName} WHERE 1=1";

        $params = [];
        $this->addFilterQuery($queryString, $countQueryString, $params, $data);

        $queryString .= ' ORDER BY created_at DESC';

        if (!empty($data['limit'])) {
            $queryString .= " LIMIT {$data['limit']}";
        }

        if (!empty($data['page']) && !empty($data['limit'])) {
            $offset = ($data['page'] - 1) * $data['limit'];
            $queryString .= " OFFSET $offset";
        }

        $stmt = self::$pdo->prepare($queryString);
        $stmt->execute($params);
        $data['rows'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = self::$pdo->prepare($countQueryString);
        $countStmt->execute($params);
        $data['total'] = (int) $countStmt->fetchColumn();

        return Response::success($data) ? : Response::error($data);
    }

    protected function addFilterQuery(
        string &$queryString,
        string &$countQueryString,
        array &$params,
        array $data
    ): void {
        if (!isset($data['filters']) || !is_array($data['filters'])) {
            return;
        }

        $whereClauses = [];
        foreach ($data['filters'] as $index => $filter) {
            $field = $filter['field'];
            $operation = $filter['operation'];
            $value = $filter['value'];

            if (!in_array($field, $this->allowedFields)) {
                continue;
            }

            $paramKey = ":param_$index";

            switch ($operation) {
                case 'eq':
                    $whereClauses[] = "$field = $paramKey";
                    $params[$paramKey] = $value;
                    break;
                case 'ne':
                    $whereClauses[] = "$field != $paramKey";
                    $params[$paramKey] = $value;
                    break;
                case 'like':
                    $whereClauses[] = "$field LIKE $paramKey";
                    $params[$paramKey] = "%$value%";
                    break;
                case 'gt':
                    $whereClauses[] = "$field > $paramKey";
                    $params[$paramKey] = $value;
                    break;
                case 'lt':
                    $whereClauses[] = "$field < $paramKey";
                    $params[$paramKey] = $value;
                    break;
                case 'in':
                    if (is_array($value) && count($value)) {
                        $inParams = [];
                        foreach ($value as $i => $val) {
                            $k = ":param_{$index}_{$i}";
                            $inParams[] = $k;
                            $params[$k] = $val;
                        }
                        $whereClauses[] = "$field IN (".implode(', ', $inParams).")";
                    }
                    break;
                case 'between':
                    if (is_array($value) || is_object($value)) {
                        $fromKey = ":param_{$index}_from";
                        $toKey = ":param_{$index}_to";

                        $valArray = (array) $value;
                        if (isset($valArray['from']) && isset($valArray['to'])) {
                            $whereClauses[] = "($field BETWEEN $fromKey AND $toKey)";
                            $params[$fromKey] = $valArray['from'];
                            $params[$toKey] = $valArray['to'];
                        }
                    }
                    break;
                default:
                    break;
            }
        }

        if ($whereClauses) {
            $whereString = ' AND '.implode(' AND ', $whereClauses);
            $queryString .= $whereString;
            $countQueryString .= $whereString;
        }
    }

    public function find(string $key, ?string $value): ?array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$key} = :value";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['value' => $value]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ? : null;
    }

    public function create(array $data): Response
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));

        $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";
        $stmt = self::$pdo->prepare($sql);

        $stmt->execute($data);
        $lastId = (int) self::$pdo->lastInsertId();

        $inserted = $this->findById($lastId);
        return $inserted
            ? Response::success($inserted)
            : Response::error();
    }

    public function findById(int $id): ?array
    {
        $stmt = self::$pdo->prepare(
            "SELECT * FROM {$this->tableName} WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ? : null;
    }

    public function deleteById(int $id): void
    {
        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function updateField(int $id, string $field, $data): void
    {
        $sql = "UPDATE {$this->tableName} SET {$field} = :value WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            'value' => $data,
            'id' => $id,
        ]);
    }

    protected function createTable(string $schema): void
    {
        if (!$this->tableExists()) {
            self::$pdo->exec(
                "CREATE TABLE IF NOT EXISTS `{$this->tableName}` ($schema)"
            );
        }
    }

    protected function tableExists(): bool
    {
        if (!self::$canUse) {
            return true;
        }

        $tablesList = self::$pdo->query(
            'SHOW TABLES LIKE "'.$this->tableName.'"'
        );
        return $tablesList->rowCount() > 0;
    }
}