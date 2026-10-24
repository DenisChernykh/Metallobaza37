<?php

namespace VG_Mailer\src\services\db;

use PDO;

class PhonesToFormsWorker extends DBWorker
{
    protected array $allowedFields = ['form_id'];

    private PhoneWorker $phoneWorker;

    public function __construct()
    {
        parent::__construct();

        $this->phoneWorker = DBWorker::make(PhoneWorker::class);
    }

    public function findPhonesByFormId(int $formId): array
    {
        $sql = "SELECT MP.* FROM {$this->phoneWorker->tableName} AS MP
            INNER JOIN {$this->tableName} AS MPF
                ON MP.id = MPF.phone_id
            WHERE MPF.form_id = :form_id";

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':form_id' => $formId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function initTable(): void
    {
        if (!$this->tableExists()) {
            $schema = "
                id INT AUTO_INCREMENT PRIMARY KEY,
                form_id INT NOT NULL,
                FOREIGN KEY (form_id) REFERENCES {$this->dbPrefix}_forms(id),
                phone_id INT NOT NULL,
                FOREIGN KEY (phone_id) REFERENCES {$this->dbPrefix}_phone(id),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ";
            $this->createTable($schema);
        }
    }

}