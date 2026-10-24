<?php

namespace VG_Mailer\src\services\db;

use VG_Mailer\src\interfaces\SenderWorkerInterface;
use VG_Mailer\src\Response;
use VG_Mailer\src\services\sendersWorkers\SenderWorkerLoader;

class FormsWorker extends DBWorker
{
    protected array $allowedFields = ['MF.created_at', 'MP.phone', 'source', 'form_name', 'status'];
    private PhoneWorker $phoneWorker;
    private PhonesToFormsWorker $phoneToFormsWorker;

    public function __construct()
    {
        parent::__construct();

        $this->phoneWorker = DBWorker::make(PhoneWorker::class);
        $this->phoneToFormsWorker = DBWorker::make(PhonesToFormsWorker::class);
    }

    public function findAll(?array $data = []): Response
    {
        $data = $this->prepareFindAllBefore($data);

        $queryString = "
            SELECT 
                MF.*, 
                (SELECT GROUP_CONCAT(phone SEPARATOR ',') 
                FROM {$this->phoneWorker->tableName} AS MP
                INNER JOIN {$this->phoneToFormsWorker->tableName} AS MPF
                    ON MP.id = MPF.phone_id
                WHERE form_id = MF.id) AS phones
            FROM {$this->tableName} AS MF
        ";
        $countQueryString = 'SELECT COUNT(*) FROM vg_mailer__forms AS MF';

        $generalQueryString = "
            LEFT JOIN {$this->phoneToFormsWorker->tableName} AS MPF ON MF.id = MPF.form_id
            LEFT JOIN {$this->phoneWorker->tableName} AS MP ON MP.id = MPF.phone_id
            WHERE 1=1";
        $queryString .= $generalQueryString;
        $countQueryString .= $generalQueryString;

        $params = [];
        $this->addFilterQuery($queryString, $countQueryString, $params, $data);

        $queryString .= ' ORDER BY MF.created_at DESC';

        if (isset($data['limit'])) {
            $queryString .= " LIMIT {$data['limit']}";
        }
        if (isset($data['page'])) {
            $offset = ($data['page'] - 1) * $data['limit'];
            $queryString .= " OFFSET $offset";
        }

        $query = self::$pdo->prepare($queryString);
        $query->execute($params);
        $data['rows'] = array_map(function ($item) {
            $senderWorkerLoader = SenderWorkerLoader::make();

            $item['form_fields'] = json_decode($item['form_fields'], true);
            $item['senders_responses'] = json_decode($item['senders_responses'], true);

            foreach ($item['senders_responses'] as $sender => $data) {
                if (!$senderWorkerLoader->isSenderWorker($sender)) {
                    continue;
                }
                /** @var SenderWorkerInterface $class */
                $class = new $sender();
                $item['senders_responses'][$sender]['label'] = $class->getLabel();
            }

            return $item;
        }, $query->fetchAll());

        $countQuery = self::$pdo->prepare($countQueryString);
        $countQuery->execute($params);
        $data['total'] = (int)$countQuery->fetchColumn();

        return Response::success($data);
    }

    public function prepareFindAllBefore(array $data = []): array
    {
        if (isset($data['filters'])) {
            foreach ($data['filters'] as $key => $item) {
                if ($item['field'] === 'phone') {
                    $item['field'] = 'MP.phone';
                } elseif ($item['field'] === 'created_at') {
                    $item['field'] = 'MF.created_at';
                }

                $data['filters'][$key] = $item;
            }
        }

        return $data;
    }

    public function create(?array $data): Response
    {
        $data['form_fields'] = json_encode($data['form_fields']);
        $data['senders_responses'] = json_encode($data['senders_responses']);

        return parent::create($data);
    }

    public function findById(int $id): ?array
    {
        $form = parent::findById($id);
        if (is_array($form)) {
            $form['form_fields'] = json_decode($form['form_fields'], true);
        }

        return $form;
    }

    protected function initTable(): void
    {
        if (!$this->tableExists()) {
            $schema = "
                id INT AUTO_INCREMENT PRIMARY KEY,
                source VARCHAR(100) NOT NULL,
                form_name VARCHAR(100) NOT NULL,
                form_fields JSON NOT NULL,
                status ENUM('sent', 'error', 'blocked') NOT NULL DEFAULT 'sent',
                senders_responses JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ";
            $this->createTable($schema);
        }
    }

}