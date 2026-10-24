<?php

namespace VG_Mailer\src\services\form;

use VG_Mailer\src\enums\MessagesEnum;
use VG_Mailer\src\models\ConfigModel;
use VG_Mailer\src\Response;

class FormServicePrepareData
{

    private ConfigModel $configModel;

    private FormService $formService;

    private array $data;

    private array $files;

    private array $validateErrors = [];

    private array $preparedFiles = [];

    private array $preparedKeys = [];

    private array $preparedData = [];

    public function __construct($data, $files)
    {
        $this->data = $data;
        $this->files = $files;

        $this->configModel = ConfigModel::make();
        $this->formService = new FormService();
    }

    public function run(): Response
    {
        if (!isset($this->data['data'])) {
            return Response::error(MessagesEnum::FORM_REQUEST_STRUCTURE_ERROR);
        }

        // Обработка данных
        foreach ($this->data['data'] as $index => $item) {
            $keyExist = isset($item['key']);
            $valueExist = isset($item['value']);
            $nameExist = isset($item['name']);
            $typeExist = isset($item['type']);

            if (!$keyExist && !$valueExist && !$nameExist && !$typeExist) {
                return Response::error(MessagesEnum::FORM_REQUEST_STRUCTURE_ERROR);
            }

            if ($this->customException($item)) {
                continue;
            }

            $this->prepareFile($item, $index);
            $this->prepareItem($item, $index);
            $this->customValidate($item, $index);
        }

        // Обработка массивов значений.
        foreach ($this->preparedKeys as $index) {
            $value = &$this->preparedData[$index]['value'];
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
        }

        // Сбрасываем ключи, что бы при json_encode(), это был массив, а не объект.
        $this->preparedData = array_values($this->preparedData);

        // Валидация провалена
        if (!empty($this->validateErrors)) {
            return Response::error([
                'validateErrors' => $this->validateErrors,
            ]);
        }

        return Response::success([
            'data' => $this->preparedData,
            'name' => $this->data['name'],
            'from' => $this->data['from'],
            'files' => $this->preparedFiles,
        ]);
    }

    private function customException($item): bool
    {
        $response = vgMailerItemException($item);
        if ($response) {
            $this->formService->deleteFormValueByKey($item['key'], $this->data);
            return true;
        }
        return false;
    }

    private function prepareFile(&$item, $index): void
    {
        $currentFileNames = $this->files['data']['name'][$index]['value'] ?? null;
        $currentFileSizes = $this->files['data']['size'][$index]['value'] ?? null;
        // Проверка существует ли файл
        if (!$currentFileSizes || !$currentFileNames) {
            return;
        }

        $maxFilesCount = $this->configModel->getMaxFilesCount();
        $maxFileSize = $this->configModel->getMaxSize() * 1024 * 1024;

        $checkFileSize = function ($sizes) use ($maxFileSize) {
            foreach ($sizes as $size) {
                if ($size > $maxFileSize) {
                    return false;
                }
            }
            return true;
        };

        // Максимальное кол-во файлов
        if (count($currentFileSizes) > $maxFilesCount) {
            $this->validateErrors[$index] = sprintf(
                MessagesEnum::MAX_FILE_COUNT_ERROR,
                $maxFilesCount
            );
        }
        // Максимальный размер
        if (!$checkFileSize($currentFileSizes)) {
            $this->validateErrors[$index] = sprintf(
                MessagesEnum::MAX_FILE_SIZE_ERROR,
                $this->configModel->getMaxSize()
            );
        }

        $item['value'] = 'Вложения: '.implode(', ', $currentFileNames);
        $this->preparedFiles[] = [
            'name' => $this->files['data']['name'][$index]['value'],
            'tmp_name' => $this->files['data']['tmp_name'][$index]['value'],
            'type' => $this->files['data']['type'][$index]['value'],
        ];
    }

    private function prepareItem($item, $index): void
    {
        $currentKey = $item['key'];
        $type = $item['type'];
        $value = $item['value'];

        // доработка под checkbox
        if ($type === 'checkbox') {
            switch ($value) {
                case 'on':
                    $item['value'] = 'Да';
                    break;
                case 'off':
                    $item['value'] = 'Нет';
                    break;
            }
        }

        // Обработка дубликата по ключу
        if (isset($this->preparedKeys[$currentKey])) {
            $originalIndex = $this->preparedKeys[$currentKey];
            $originalItem = &$this->preparedData[$originalIndex];

            if (is_string($originalItem['value'])) {
                $originalItem['value'] = [$originalItem['value']];
            }
            $originalItem['value'][] = $item['value'];
            return;
        }

        $this->preparedKeys[$currentKey] = $index;
        $this->preparedData[$index] = $item;
    }

    private function customValidate(&$item, $index): void
    {
        $response = vgMailerItemValidate($item);
        if ($response->isError()) {
            $this->validateErrors[$index] = $response->getData();
        }
    }

}