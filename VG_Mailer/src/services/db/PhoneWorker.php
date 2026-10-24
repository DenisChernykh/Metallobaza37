<?php

namespace VG_Mailer\src\services\db;

use VG_Mailer\src\utils\StringUtils;

class PhoneWorker extends DBWorker
{

    public function createMany(array $rawData): array
    {
        $createdIds = [];

        foreach ($rawData['data'] as $rawDataItem) {
            if ($rawDataItem["key"] === "phone" || $rawDataItem["type"] === "tel") {
                $value = explode(",", $rawDataItem["value"]);
                foreach ($value as $phone) {
                    $phone = StringUtils::filterDigits($phone);

                    $existingPhone = $this->find('phone', $phone);
                    if ($existingPhone) {
                        $createdIds[] = $existingPhone['id'];
                        continue;
                    }

                    $dataToAdd = ["phone" => $phone];
                    $createdIds[] = $this->create($dataToAdd)->getData()['id'];
                }
            }
        }

        return $createdIds;
    }

    protected function initTable(): void
    {
        if (!$this->tableExists()) {
            $schema = "
                id INT AUTO_INCREMENT PRIMARY KEY,
                phone VARCHAR(20) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ";
            $this->createTable($schema);
        }
    }

}