<?php

namespace VG_Mailer\src\services\db;


use Exception;
use VG_Mailer\src\enums\MessagesEnum;
use VG_Mailer\src\Response;
use VG_Mailer\src\services\sendersWorkers\SenderWorkerLoader;
use VG_Mailer\src\utils\ArrayUtils;
use VG_Mailer\src\utils\StringUtils;

class PhoneBlackListWorker extends DBWorker
{
    protected array $allowedFields = ['phone', 'created_at'];

    public function create(array $data, bool $returnExceptions = false): Response
    {
        try {
            $preparedData = [
                'phone' => $data['phone'] ?? null,
                'services' => ArrayUtils::stripslashes(json_encode($data['services'] ?? [])),
            ];

            if (!$preparedData['phone']) {
                throw new Exception(MessagesEnum::PHONE_IS_INVALID);
            }

            $preparedData['phone'] = StringUtils::filterDigits($preparedData['phone'], true);
            if (strlen($preparedData['phone']) !== 11) {
                throw new Exception(MessagesEnum::PHONE_IS_INVALID);
            }

            if ($this->find('phone', $preparedData['phone'])) {
                throw new Exception(MessagesEnum::PHONE_ALREADY_BLOCKED);
            }

            return parent::create($preparedData);
        } catch (Exception $exception) {
            if ($returnExceptions) {
                return Response::error($exception->getMessage());
            } else {
                throw $exception;
            }
        }
    }

    public function findAll(?array $data = []): Response
    {
        $data = $this->prepareFindAllBefore($data);
        $response = parent::findAll($data);
        if ($response->isError()) {
            return $response;
        }
        return $this->prepareFindOneAfter($response);
    }

    private function prepareFindAllBefore(?array $data): array
    {
        if (!$data || !isset($data['filters'])) {
            return [];
        }

        foreach ($data['filters'] as $index => $filter) {
            if ($filter['field'] === 'phone') {
                $filter['value'] = StringUtils::filterDigits($filter['value'], true);
            }
            $data['filters'][$index] = $filter;
        }

        return $data;
    }

    private function prepareFindOneAfter(Response $response): Response
    {
        $senderWorkerLoader = SenderWorkerLoader::make();
        $allSenderWorkers = $senderWorkerLoader->loadWorkers(true);

        $data = $response->getData();
        $data['rows'] = array_map(function ($el) use ($allSenderWorkers) {
            $blockedData = json_decode($el['services'], true);
            $el['services'] = [];

            foreach ($allSenderWorkers as $senderWorker) {
                $class = new $senderWorker();

                $el['services'][] = [
                    'namespace' => $senderWorker,
                    'label' => $class->getLabel(),
                    'active' => in_array($senderWorker, $blockedData),
                ];
            }

            return $el;
        }, $data['rows']);

        return Response::success($data);
    }

    /**
     * @param array|string $phone
     * @return array|null
     */
    public function getByPhone($phone): ?array
    {
        if (is_string($phone) || is_int($phone)) {
            $stmt = self::$pdo->prepare(
                "SELECT * FROM {$this->tableName} WHERE ? LIKE REPLACE(phone, '*', '_')"
            );
            $stmt->execute([StringUtils::filterDigits($phone, true)]);
        } elseif (is_array($phone) && !empty($phone)) {
            $tempPhonesSql = '';
            foreach ($phone as &$el) {
                $el = StringUtils::filterDigits($el, true);
                if ($tempPhonesSql === '') {
                    $tempPhonesSql = 'SELECT ? AS phone';
                } else {
                    $tempPhonesSql .= ' UNION ALL SELECT ?';
                }
            }
            $sql = "
                SELECT DISTINCT t1.* FROM {$this->tableName} AS t1
                INNER JOIN ({$tempPhonesSql}) AS temp
                    ON temp.phone LIKE REPLACE(t1.phone, '*', '_')
                WHERE JSON_LENGTH(t1.services) > 0
            ";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($phone);
        } else {
            return [];
        }

        return array_map(function ($item) {
            $item['services'] = json_decode($item['services'], true);
            return $item;
        }, $stmt->fetchAll());
    }

    protected function initTable(): void
    {
        if (!$this->tableExists()) {
            $schema = "
                id INT AUTO_INCREMENT PRIMARY KEY,
                phone VARCHAR(20) UNIQUE,
                services JSON NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ";
            $this->createTable($schema);
        }
    }
}